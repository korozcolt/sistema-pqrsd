<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BumpVersionCommand extends Command
{
    protected $signature = 'version:bump
                            {type=patch : The type of version bump (major, minor, patch)}
                            {--message= : Optional message for the version bump}
                            {--no-commit : Do not create a git commit}
                            {--dry-run : Show what would happen without making changes}';

    protected $description = 'Bump the application version following Semantic Versioning';

    public function handle()
    {
        $type = $this->argument('type');
        $dryRun = $this->option('dry-run');

        // Validar tipo
        if (!in_array($type, ['major', 'minor', 'patch'])) {
            $this->error('Invalid version type. Must be: major, minor, or patch');
            return 1;
        }

        // Obtener versión actual
        $currentVersion = $this->getCurrentVersion();
        $this->info("Current version: {$currentVersion}");

        // Calcular nueva versión
        $newVersion = $this->calculateNewVersion($currentVersion, $type);
        $this->info("New version: {$newVersion}");

        if ($dryRun) {
            $this->warn('DRY RUN - No changes will be made');
            return 0;
        }

        // Preguntar confirmación
        if (!$this->confirm('Do you want to continue?', true)) {
            $this->info('Version bump cancelled.');
            return 0;
        }

        // Actualizar VERSION file
        $this->updateVersionFile($newVersion);
        $this->info('✓ VERSION file updated');

        // Actualizar config/version.php
        $this->updateConfigFile($newVersion);
        $this->info('✓ config/version.php updated');

        // Actualizar .env (opcional)
        $this->updateEnvFile($newVersion);
        $this->info('✓ .env file updated');

        // Crear commit de git
        if (!$this->option('no-commit')) {
            $message = $this->option('message') ?: "chore: bump version to {$newVersion}";
            $this->createGitCommit($newVersion, $message);
            $this->info('✓ Git commit created');

            // Crear tag
            $this->createGitTag($newVersion);
            $this->info('✓ Git tag created');
        }

        $this->info('');
        $this->info("🎉 Version bumped successfully from {$currentVersion} to {$newVersion}");

        if (!$this->option('no-commit')) {
            $this->warn('Don\'t forget to push the tag: git push origin v' . $newVersion);
        }

        return 0;
    }

    private function getCurrentVersion(): string
    {
        $versionFile = config('version.file', base_path('VERSION'));

        if (File::exists($versionFile)) {
            return trim(File::get($versionFile));
        }

        return '0.0.0';
    }

    private function calculateNewVersion(string $current, string $type): string
    {
        [$major, $minor, $patch] = explode('.', $current);

        switch ($type) {
            case 'major':
                return ($major + 1) . '.0.0';
            case 'minor':
                return $major . '.' . ($minor + 1) . '.0';
            case 'patch':
                return $major . '.' . $minor . '.' . ($patch + 1);
        }

        return $current;
    }

    private function updateVersionFile(string $version): void
    {
        $versionFile = config('version.file', base_path('VERSION'));
        File::put($versionFile, $version);
    }

    private function updateConfigFile(string $version): void
    {
        $configFile = config_path('version.php');
        $content = File::get($configFile);

        // Actualizar version
        $content = preg_replace(
            "/'version' => env\('APP_VERSION', '[^']+'\)/",
            "'version' => env('APP_VERSION', '{$version}')",
            $content
        );

        // Actualizar release_date
        $today = date('Y-m-d');
        $content = preg_replace(
            "/'release_date' => env\('APP_RELEASE_DATE', '[^']+'\)/",
            "'release_date' => env('APP_RELEASE_DATE', '{$today}')",
            $content
        );

        File::put($configFile, $content);
    }

    private function updateEnvFile(string $version): void
    {
        $envFile = base_path('.env');

        if (!File::exists($envFile)) {
            return;
        }

        $content = File::get($envFile);

        // Si existe APP_VERSION, actualizarla
        if (preg_match('/^APP_VERSION=/m', $content)) {
            $content = preg_replace(
                '/^APP_VERSION=.*/m',
                "APP_VERSION={$version}",
                $content
            );
        } else {
            // Si no existe, agregarla
            $content .= "\nAPP_VERSION={$version}\n";
        }

        // Actualizar fecha
        $today = date('Y-m-d');
        if (preg_match('/^APP_RELEASE_DATE=/m', $content)) {
            $content = preg_replace(
                '/^APP_RELEASE_DATE=.*/m',
                "APP_RELEASE_DATE={$today}",
                $content
            );
        } else {
            $content .= "APP_RELEASE_DATE={$today}\n";
        }

        File::put($envFile, $content);
    }

    private function createGitCommit(string $version, string $message): void
    {
        exec('git add VERSION config/version.php .env 2>&1', $output, $return);

        if ($return !== 0) {
            $this->warn('Could not stage files for git commit');
            return;
        }

        $escapedMessage = escapeshellarg($message);
        exec("git commit -m {$escapedMessage} 2>&1", $output, $return);

        if ($return !== 0) {
            $this->warn('Could not create git commit');
        }
    }

    private function createGitTag(string $version): void
    {
        $tag = "v{$version}";
        $message = escapeshellarg("Release version {$version}");

        exec("git tag -a {$tag} -m {$message} 2>&1", $output, $return);

        if ($return !== 0) {
            $this->warn('Could not create git tag');
        }
    }
}
