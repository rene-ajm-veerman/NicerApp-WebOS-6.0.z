<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Uploader Client</title>
  <style>
    body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
    form { max-width: 500px; margin: auto; }
    input[type="file"] { display: block; margin: 10px 0; }
    button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    #progress { margin-top: 20px; }
    .bar { height: 20px; background: #ddd; border-radius: 10px; overflow: hidden; }
    .fill { height: 100%; background: #28a745; width: 0%; transition: width 0.3s; }
    #status { margin-top: 10px; color: #333; }
  </style>
</head>
<body>
  <form id="uploadForm">
    <label for="files">Select files:</label>
    <input type="file" id="files" name="files[]" multiple>
    <button type="submit">Upload</button>
  </form>

  <div id="progress">
    <div class="bar"><div class="fill" id="progressBar"></div></div>
    <div id="status">Ready</div>
  </div>

  <script>
    const auth = btoa('admin:supersecret123'); // CHANGE THIS!
    const ws = new WebSocket('ws://127.0.0.1:8082'); // Use wss:// in prod

    ws.onopen = () => console.log('WS connected');
    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      if (data.type === 'progress') {
        document.getElementById('progressBar').style.width = `${data.percent}%`;
        document.getElementById('status').textContent = `Uploading: ${data.percent}%`;
      } else if (data.type === 'complete') {
        document.getElementById('status').textContent = 'Upload complete!';
      } else if (data.type === 'error') {
        document.getElementById('status').textContent = `Error: ${data.message}`;
      }
    };

    document.getElementById('uploadForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const files = document.getElementById('files').files;
      if (files.length === 0) return;

      const formData = new FormData();
      for (let file of files) {
        formData.append('files[]', file);
      }

      try {
        document.getElementById('status').textContent = 'Starting upload...';
        document.getElementById('progressBar').style.width = '0%';

        const res = await fetch('https://127.0.0.1:8081/upload', { // Accept self-signed cert in browser dev tools
          method: 'POST',
          body: formData,
          headers: { 'Authorization': `Basic ${auth}` }
        });

        if (res.ok) {
          const json = await res.json();
          console.log('Success:', json);
        } else {
          document.getElementById('status').textContent = 'Upload failed';
        }
      } catch (err) {
        document.getElementById('status').textContent = `Error: ${err.message}`;
      }
    });
  </script>
</body>
</html>
