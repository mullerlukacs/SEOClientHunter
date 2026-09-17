import { spawn, execSync } from 'child_process';
import http from 'http';
import fs from 'fs';

const PHP_PORT = 8089;
const PORT = parseInt(process.env.PORT || '3000', 10);

console.log('🚀 Starting SEO Client Hunter backend server...');

// Ensure PHP is available
function ensurePhpInstalled() {
  try {
    execSync('which php || which php8.2', { stdio: 'pipe' });
    console.log('PHP binary verified.');
  } catch (e) {
    console.log('PHP not detected in runtime container. Auto-installing PHP 8.2 CLI and SQLite extensions...');
    try {
      execSync('apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends php8.2-cli php8.2-sqlite3 php8.2-curl php8.2-mbstring && ln -sf /usr/bin/php8.2 /usr/bin/php', { stdio: 'inherit' });
      console.log('PHP 8.2 environment auto-configured successfully.');
    } catch (installErr) {
      console.error('Warning during PHP auto-installation:', installErr);
    }
  }
}

ensurePhpInstalled();

// Resolve php binary path
let phpBin = 'php';
if (!fs.existsSync('/usr/bin/php') && fs.existsSync('/usr/bin/php8.2')) {
  phpBin = '/usr/bin/php8.2';
}

let phpProcess = null;

function startPhpServer() {
  console.log(`Spawning ${phpBin} built-in server on 127.0.0.1:${PHP_PORT}...`);
  phpProcess = spawn(phpBin, ['-S', `127.0.0.1:${PHP_PORT}`, '-t', '.', 'public/index.php'], {
    stdio: 'inherit',
    env: { ...process.env }
  });

  phpProcess.on('error', (err) => {
    console.error('PHP server process error:', err);
  });

  phpProcess.on('exit', (code, signal) => {
    console.log(`PHP server exited with code ${code}, restarting in 1s...`);
    setTimeout(startPhpServer, 1000);
  });
}

startPhpServer();

process.on('SIGINT', () => {
  if (phpProcess) phpProcess.kill();
  process.exit();
});

process.on('SIGTERM', () => {
  if (phpProcess) phpProcess.kill();
  process.exit();
});

// Create reverse proxy server on external port 3000
const server = http.createServer((req, res) => {
  const options = {
    hostname: '127.0.0.1',
    port: PHP_PORT,
    path: req.url,
    method: req.method,
    headers: req.headers
  };

  const proxyReq = http.request(options, (proxyRes) => {
    res.writeHead(proxyRes.statusCode || 200, proxyRes.headers);
    proxyRes.pipe(res, { end: true });
  });

  proxyReq.on('error', (err) => {
    if (!res.headersSent) {
      res.writeHead(502, { 'Content-Type': 'text/html' });
      res.end(`
        <div style="font-family: sans-serif; padding: 40px; text-align: center;">
          <h2 style="color: #2563eb;">Starting SEO Client Hunter Engine...</h2>
          <p style="color: #64748b;">Initializing PHP 8.2 environment and SQLite database layer. Refreshing in a few seconds...</p>
          <script>setTimeout(() => window.location.reload(), 2000);</script>
        </div>
      `);
    }
  });

  req.on('error', (err) => {
    console.error('Client request error:', err);
  });

  req.pipe(proxyReq, { end: true });
});

// Listen on port 3000
setTimeout(() => {
  server.listen(PORT, '0.0.0.0', () => {
    console.log(`✅ SEO Client Hunter live and listening on http://0.0.0.0:${PORT}`);
  });
}, 800);

