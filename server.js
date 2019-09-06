var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);

const bodyParser = require("body-parser");
app.use(bodyParser.urlencoded({
    extended: true
}));
app.use(bodyParser.json({limit: '10mb'}));

server.listen(8088);

app.post('/storeUpdate', function (req, res) {
  io.emit("storeUpdate", { data: req.body.data })
  res.send("ok")
});
  