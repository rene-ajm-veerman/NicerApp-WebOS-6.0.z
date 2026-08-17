<!DOCTYPE html>
<html>
<head>
  <title>Upload with Progress</title>
</head>
<body>
  <form id="uploadForm" enctype="multipart/form-data">
    <input type="file" name="file" id="fileInput">
    <button type="submit">Upload</button>
  </form>

  <div id="progress">Progress: 0%</div>

  <script>
    const ws = new WebSocket('ws://127.0.0.1:8082/ws'); // or wss:// in prod

    ws.onmessage = (event) => {
      const data = JSON.parse(event.data);
      if (data.type === 'progress') {
        document.getElementById('progress').textContent = `Progress: ${data.progress}%`;
      } else if (data.type === 'complete') {
        document.getElementById('progress').textContent = 'Upload complete!';
      }
    };

    document.getElementById('uploadForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const file = document.getElementById('fileInput').files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append('file', file);

      await fetch('https://127.0.0.1:8081/upload', {
        method: 'POST',
        body: formData,
        headers: {
          'Authorization': 'Basic ' + btoa('admin:supersecret123')
        }
      });

      // Server will push progress via WebSocket
    });
  </script>
</body>
</html>
