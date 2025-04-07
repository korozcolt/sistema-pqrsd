<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class NormalizeUserEmails extends Command
{
    protected $signature = 'users:normalize-emails';
    protected $description = 'Normaliza los emails de los usuarios eliminando tildes y caracteres especiales';

    public function handle()
    {
        $users = User::all();
        $updated = 0;
        $skipped = 0;

        $this->info("Comenzando normalización de emails...");
        $this->newLine();

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $user) {
            $originalEmail = $user->getOriginal('email');
            $normalizedEmail = $this->removeAccents(strtolower($originalEmail));

            if ($originalEmail !== $normalizedEmail) {
                // Verificar que no exista otro usuario con el email normalizado
                $exists = User::where('email', $normalizedEmail)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if (!$exists) {
                    // Desactivar temporalmente los eventos para evitar triggers no deseados
                    $user->timestamps = false;
                    $user->email = $normalizedEmail;
                    $user->save();
                    $user->timestamps = true;

                    $updated++;
                    $this->line("<info>Normalizado:</info> {$originalEmail} -> {$normalizedEmail}");
                } else {
                    $skipped++;
                    $this->line("<comment>Conflicto:</comment> No se pudo normalizar {$originalEmail} porque ya existe {$normalizedEmail}");
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Proceso completado:");
        $this->line("- Total de usuarios procesados: " . count($users));
        $this->line("- Emails normalizados: {$updated}");
        $this->line("- Emails con conflictos (omitidos): {$skipped}");
        $this->line("- Emails sin cambios: " . (count($users) - $updated - $skipped));

        return Command::SUCCESS;
    }

    /**
     * Remueve acentos y caracteres especiales de un string.
     *
     * @param string $string
     * @return string
     */
    private function removeAccents(string $string): string
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        $chars = [
            // Decomposición para caracteres latinos
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ý' => 'y',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ý' => 'Y',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u', 'ÿ' => 'y',
            'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U', 'Ÿ' => 'Y',
            'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',
            'Â' => 'A', 'Ê' => 'E', 'Î' => 'I', 'Ô' => 'O', 'Û' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
            'ç' => 'c', 'Ç' => 'C',
            // Más caracteres especiales que pudieran aparecer en nombres o dominios
            'ã' => 'a', 'õ' => 'o', 'Ã' => 'A', 'Õ' => 'O',
            'ø' => 'o', 'Ø' => 'O',
            'æ' => 'ae', 'Æ' => 'AE',
            'œ' => 'oe', 'Œ' => 'OE',
            'ð' => 'd', 'Ð' => 'D',
            'þ' => 'th', 'Þ' => 'TH',
            // Caracteres con diéresis
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U',
            // Caracteres con tilde grave
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
            'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
            // Caracteres con tilde circunfleja
            'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',
            'Â' => 'A', 'Ê' => 'E', 'Î' => 'I', 'Ô' => 'O', 'Û' => 'U',
            // Caracteres con virgulilla
            'ã' => 'a', 'ñ' => 'n', 'õ' => 'o',
            'Ã' => 'A', 'Ñ' => 'N', 'Õ' => 'O',
            // Caracteres específicos de idiomas
            'ß' => 'ss', // Alemán
            'ş' => 's', 'Ş' => 'S', // Turco
            'ğ' => 'g', 'Ğ' => 'G', // Turco
            'ı' => 'i', 'İ' => 'I', // Turco
            'ć' => 'c', 'Ć' => 'C', // Polaco, Croata, etc.
            'č' => 'c', 'Č' => 'C', // Checo, Eslovaco, etc.
            'đ' => 'd', 'Đ' => 'D', // Croata, Vietnamita
            'ł' => 'l', 'Ł' => 'L', // Polaco
            'ś' => 's', 'Ś' => 'S', // Polaco
            'ź' => 'z', 'Ź' => 'Z', // Polaco
            'ż' => 'z', 'Ż' => 'Z', // Polaco
            'ő' => 'o', 'Ő' => 'O', // Húngaro
            'ű' => 'u', 'Ű' => 'U', // Húngaro
        ];

        // Primero intentamos con strtr() que es más rápido
        $string = strtr($string, $chars);

        // Si aún quedan caracteres especiales, usamos transliterator
        if (preg_match('/[\x80-\xff]/', $string) && function_exists('transliterator_transliterate')) {
            $string = transliterator_transliterate('Any-Latin; Latin-ASCII', $string);
        }

        // Eliminamos cualquier caracter que no sea alfanumérico, punto, guion o guion bajo
        $string = preg_replace('/[^a-z0-9\.\-\_\@]/i', '', $string);

        return $string;
    }
}
