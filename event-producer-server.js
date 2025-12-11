// Simple HTTP server to publish task-completed events to RabbitMQ
// POST /events/task-completed with JSON body { taskId, title, completedBy, completedAt }

const http = require("http");
const amqp = require("amqplib");

const RABBITMQ_URL = process.env.RABBITMQ_URL || "amqp://localhost";
const QUEUE_NAME = process.env.TASK_EVENTS_QUEUE || "TASK_EVENTS_QUEUE";
const PORT = Number(process.env.PORT || 3001);

let channel;

async function initRabbit() {
  const conn = await amqp.connect(RABBITMQ_URL);
  channel = await conn.createChannel();
  await channel.assertQueue(QUEUE_NAME, { durable: true });
  console.log(
    `[ProducerServer] RabbitMQ listo en ${RABBITMQ_URL}, cola ${QUEUE_NAME}`
  );
}

function sendEvent(payload) {
  const body = Buffer.from(JSON.stringify(payload));
  const ok = channel.sendToQueue(QUEUE_NAME, body, { persistent: true });
  return ok;
}

function createServer() {
  const server = http.createServer(async (req, res) => {
    // Only accept POST /events/task-completed
    if (req.method === "POST" && req.url === "/events/task-completed") {
      try {
        let data = "";
        req.on("data", (chunk) => (data += chunk));
        req.on("end", () => {
          try {
            const now = new Date().toISOString();
            const base = JSON.parse(data || "{}");
            const payload = {
              taskId: Number(base.taskId) || Math.floor(Math.random() * 10000),
              title: base.title || "Tarea completada",
              completedBy: base.completedBy || "Usuario",
              completedAt: base.completedAt || now,
            };
            const ok = sendEvent(payload);
            res.writeHead(200, { "Content-Type": "application/json" });
            res.end(JSON.stringify({ ok, queued: payload }));
          } catch (e) {
            res.writeHead(400, { "Content-Type": "application/json" });
            res.end(
              JSON.stringify({ error: "JSON inválido", details: String(e) })
            );
          }
        });
      } catch (err) {
        res.writeHead(500, { "Content-Type": "application/json" });
        res.end(
          JSON.stringify({ error: "Error interno", details: String(err) })
        );
      }
      return;
    }

    // Health check
    if (req.method === "GET" && req.url === "/health") {
      res.writeHead(200, { "Content-Type": "application/json" });
      res.end(JSON.stringify({ status: "ok" }));
      return;
    }

    res.writeHead(404, { "Content-Type": "application/json" });
    res.end(JSON.stringify({ error: "Not Found" }));
  });

  server.listen(PORT, () => {
    console.log(`[ProducerServer] Escuchando en http://localhost:${PORT}`);
  });
}

(async () => {
  try {
    await initRabbit();
    createServer();
  } catch (e) {
    console.error("[ProducerServer] Error iniciando:", e);
    process.exit(1);
  }
})();
