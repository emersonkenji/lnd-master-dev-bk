const express = require('express');
const fs = require('fs-extra');
const path = require('path');
const cors = require('cors');

const app = express();
const port = 5000;

app.use(cors());

app.get('/api/tasks', async (req, res) => {
  try {
    const data = await fs.readFile(path.join(__dirname, 'tasks.json'), 'utf8');
    const tasks = JSON.parse(data);
    res.json(tasks);
  } catch (err) {
    res.status(500).json({ message: 'Erro ao ler o arquivo de tarefas.' });
  }
});

app.listen(port, () => {
  console.log(`Servidor rodando em http://localhost:${port}`);
});

