<?php
declare(strict_types=1);

use DateTime;
use PDO;

class NotificationEngine
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Gera TODAS as notificações que devem ser exibidas HOJE
     */
    public function gerarNotificacoesDoDia(
        int $usuarioId,
        DateTime $agora
    ): array {
        $notificacoes = [];

        $dia = (int) $agora->format('d');
        $mes = (int) $agora->format('m');

        // 1️⃣ Fechamento de faturamento
        if ($dia <= 2) {
            $notificacoes[] = $this->criar(
                'fechamento_faturamento',
                'Fechamento de faturamento do mês anterior',
                'normal'
            );
        } elseif ($dia > 2) {
            $notificacoes[] = $this->criar(
                'fechamento_faturamento',
                'Fechamento de faturamento do mês anterior',
                'urgente'
            );
        }

        // 2️⃣ PG-DAS
        if ($dia >= 3 && $dia <= 5) {
            $notificacoes[] = $this->criar(
                'pgdas',
                'Declarar faturamento no PG-DAS',
                'normal'
            );
        } elseif ($dia > 5) {
            $notificacoes[] = $this->criar(
                'pgdas',
                'Declarar faturamento no PG-DAS',
                'urgente'
            );
        }

        // 3️⃣ DAS
        if ($dia >= 15 && $dia <= 20) {
            $notificacoes[] = $this->criar(
                'das',
                'Pagar DAS do Simples Nacional',
                'normal'
            );
        } elseif ($dia > 20) {
            $notificacoes[] = $this->criar(
                'das',
                'Pagar DAS do Simples Nacional',
                'urgente'
            );
        }

        // 4️⃣ DEFIS (março)
        if ($mes === 3) {
            $notificacoes[] = $this->criar(
                'defis',
                'Entregar DEFIS anual',
                'normal'
            );
        }

        // 5️⃣ Taxa Prefeitura (janeiro)
        if ($mes === 1) {
            $notificacoes[] = $this->criar(
                'taxa_prefeitura',
                'Pagar Taxa de Funcionamento da Prefeitura',
                'normal'
            );
        }

        return $notificacoes;
    }

    private function criar(
        string $codigo,
        string $titulo,
        string $prioridade
    ): array {
        return [
            'codigo'     => $codigo,
            'titulo'     => $titulo,
            'prioridade' => $prioridade
        ];
    }
}
