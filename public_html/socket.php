<script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>
<script>
  var socket = io('http://185.65.245.45:7080');
  socket.on('news', function (data) {
    console.log(data);
    socket.emit('my other event', { my: 'data' });
  });
</script>