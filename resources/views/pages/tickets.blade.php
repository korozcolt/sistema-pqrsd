@extends('layouts.page')
@section('content-page')
    <div class="page-banner bg-2">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="container">
                    <div class="page-content">
                        <h2>Sistema de Tickets</h2>
                        <ul>
                            <li><a href="{{ url('/') }}">Inicio</a></li>
                            <li>Tickets</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-8">
        <livewire:ticket-system />
    </div>
@endsection
