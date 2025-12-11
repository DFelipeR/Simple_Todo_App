// Notification Worker for TASK_EVENTS_QUEUE using RabbitMQ (amqplib)
// Node.js >=14 recommended

const amqp = require("amqplib");

// Configuration via environment variables with sensible defaults
const RABBITMQ_URL = process.env.RABBITMQ_URL || "amqp://localhost";
const QUEUE_NAME = process.env.TASK_EVENTS_QUEUE || "TASK_EVENTS_QUEUE";

async function startWorker() {
  let connection;
  try {
    // 1) Connect to RabbitMQ server
    connection = await amqp.connect(RABBITMQ_URL);
    console.log(`[Worker] Conectado a RabbitMQ en ${RABBITMQ_URL}`);

    // 2) Create a channel
    const channel = await connection.createChannel();

    // 3) Ensure the queue exists (idempotent)
    await channel.assertQueue(QUEUE_NAME, {
      durable: true, // persist messages to disk to survive broker restarts
    });
    console.log(`[Worker] Cola asegurada: ${QUEUE_NAME}`);

    // Optional: process one message at a time per consumer to simulate workload control
    channel.prefetch(1);

    // 4) Start consuming messages
    await channel.consume(
      QUEUE_NAME,
      async (msg) => {
        if (!msg) return; // Safety: could be null on consumer cancel

        try {
          const content = msg.content.toString();
          const payload = JSON.parse(content);

          if (!payload.taskId || !payload.title || !payload.completedBy) {
            throw new Error("Payload incompleto");
          }

          // Simulate sending congratulatory email
          const { taskId, title, completedBy, completedAt } = payload;
          console.log(
            `[Worker] Enviando email de felicitación: Tarea #${taskId} "${title}" completada por ${completedBy} en ${completedAt}`
          );

          // Simulate processing delay (optional)
          // await new Promise((res) => setTimeout(res, 250));

          // 5) Acknowledge message after successful processing
          channel.ack(msg);
          console.log(`[Worker] Mensaje ACK: Tarea #${taskId}`);
        } catch (err) {
          console.error("[Worker] Error procesando mensaje:", err);

          // In case of parsing/processing error, do not ack
          // Optionally, you could implement retry or dead-letter here
          // Nack with requeue=false to avoid infinite loops
          try {
            channel.nack(msg, false, false);
            console.warn(
              "[Worker] Mensaje NACK sin requeue (dead-letter si está configurado)"
            );
          } catch (nackErr) {
            console.error("[Worker] Error al NACK:", nackErr);
          }
        }
      },
      {
        noAck: false, // we will manually ack messages after processing
      }
    );

    console.log(
      `[Worker] Esperando mensajes en "${QUEUE_NAME}"... (Ctrl+C para salir)`
    );

    // Handle graceful shutdown
    const shutdown = async () => {
      console.log("\n[Worker] Cerrando conexión...");
      try {
        await channel.close();
        await connection.close();
      } catch (e) {
        // ignore
      }
      process.exit(0);
    };

    process.on("SIGINT", shutdown);
    process.on("SIGTERM", shutdown);
  } catch (error) {
    console.error("[Worker] No se pudo iniciar el worker:", error);
    if (connection) {
      try {
        await connection.close();
      } catch {}
    }
    process.exit(1);
  }
}

startWorker();
