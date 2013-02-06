var app = require('http').createServer(function(req, res){
  fs.readFile(__dirname + '/index.html', function (err, data) {
    if (err) {
      res.writeHead(500);
      return res.end('Error loading index.html');
    }
    
    res.writeHead(200);
    res.end(data);
  });
});

var io       = require('socket.io').listen(app);
var fs       = require('fs');
var amqp     = require('amqp');
var rabbitMQ = amqp.createConnection({ host: 'localhost', port: 5672},
                                     { defaultExchangeName: "voucherExchange"});

app.listen(12345, '192.168.33.10');

rabbitMQ.on('ready', function() {
  console.log('Connected to RabbitMQ');
  io.sockets.on('connection', function (socket) {
    console.log('Socket connected: ' + socket.id);
    rabbitMQ.queue('datafeed', { }, function(q) {
      q.bind('voucherExchange', '#'); // Catch all messages    
      q.subscribe(function (message) {
        console.log('message rcvd' + message.data.toString());
        obj = message.data.toString();
        socket.emit('news', {data: obj});
      });
    });
  });
});