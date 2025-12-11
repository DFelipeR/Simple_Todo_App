// Simple producer to send a test message to TASK_EVENTS_QUEUE (with CLI flags)
const amqp = require("amqplib");

const RABBITMQ_URL = process.env.RABBITMQ_URL || "amqp://localhost";
const QUEUE_NAME = process.env.TASK_EVENTS_QUEUE || "TASK_EVENTS_QUEUE";

// Parse simple CLI flags: --title, --by, --id, --count
function parseArgs(argv) {
  const args = {};
  for (let i = 2; i < argv.length; i++) {
    const m = argv[i].match(/^--([^=]+)=(.*)$/);
    if (m) args[m[1]] = m[2];
  }
  return args;
}

async function sendTest() {
  let connection;
  try {
    connection = await amqp.connect(RABBITMQ_URL);
    const channel = await connection.createChannel();

    await channel.assertQueue(QUEUE_NAME, { durable: true });

    const flags = parseArgs(process.argv);
    const count = Number(flags.count || 1);
    const baseMsg = {
      taskId: flags.id ? Number(flags.id) : Math.floor(Math.random() * 10000),
      title: flags.title || "Completar documentación de microservicios",
      completedBy: flags.by || "Desarrollador",
      completedAt: new Date().toISOString(),
    };

    for (let i = 0; i < count; i++) {
      const msg = {
        ...baseMsg,
        taskId: flags.id ? Number(flags.id) : Math.floor(Math.random() * 10000),
        completedAt: new Date().toISOString(),
      };
      const ok = channel.sendToQueue(
        QUEUE_NAME,
        Buffer.from(JSON.stringify(msg)),
        { persistent: true }
      );
      console.log(
        `[Producer] Mensaje enviado (${
          ok ? "OK" : "FALLÓ"
        }) a ${QUEUE_NAME}: #${msg.taskId}`
      );
    }

    await channel.close();
    await connection.close();
  } catch (err) {
    console.error("[Producer] Error enviando mensaje:", err);
    if (connection) {
      try {
        await connection.close();
      } catch {}
    }
    process.exit(1);
  }
}

sendTest();
