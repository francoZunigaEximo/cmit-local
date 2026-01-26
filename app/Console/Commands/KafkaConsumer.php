<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RdKafka\Conf;
use RdKafka\KafkaConsumer as RdKafkaConsumer;

class KafkaConsumer extends Command
{
    protected $signature = "kafka:consumo-migracion";
    protected $description = "Consumer principal de aplicacion CMIT Actual";

    protected $processors = [];

    public function handler()
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', '192.168.1.5');
        $conf->set('group_id', 'migracion-a-laravel');
        $conf->set('auto.offset.reset', 'earliest');
        $conf->set('enable.auto.commit', true);
        $conf->set('auto.commit.interval.ms', 5000);

        $consumer = new RdKafkaConsumer($conf);
        $consumer->subscribe(['^dbsoftactual.db_gestion.*']);

        $this->info("Iniciando sincronización");

        while(true) {
            $message = $consumer->consume(120);

            switch($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $this->routeMessage($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    $this->info("End of partition reached");
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $this->info("Timed out");
                    break;
                default:
                    $this->error("Error: " . $message->err);
                    break;
            }
        }
    }

    protected function routeMessage($message)
    {
        $data = json_decode($message->payload, true);
        if (!$data || !isset($data['payload'])) return;

        $parts = explode('.', $message->topic_name);
        $tableName = end($parts);

        if (!array_key_exists($tableName, $this->processors)) {
            return;
        }

        try {
            $processorClass = $this->processors[$tableName];
            $processor = new $processorClass();

            $payload = $data['payload'];
            $op = $payload['op'];

            if ($op === 'c') { // crear
                $processor->handleInsert($payload['after']);
            } elseif ($op === 'u') { // actualizar
                $processor->handleUpdate($payload['before'], $payload['after']);
            } elseif ($op === 'd') { // eliminar
                $processor->handleDelete($payload['before']);
            }

        } catch (\Exception $e) {
            $this->error("Error procesando tabla {$tableName}: " . $e->getMessage());
            Log::error("Error replicación Kafka [{$tableName}]: " . $e->getMessage());
        }
    }

}