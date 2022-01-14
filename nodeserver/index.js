const httpServer = require("http").createServer();
const io = require("socket.io")(httpServer, {
  allowEIO3: true,
  cors: {
    origin: "http://172.22.21.86/",
    methods: ["GET", "POST"],
    credentials: true,
  },
});
httpServer.listen(8081, function () {
  console.log("listening on *:8081");
});
io.on("connection", function (socket) {
  console.log(`client ${socket.id} has connected`);
});

io.on("connection", function (socket) {
  console.log(`client ${socket.id} has connectede`);
  socket.on("newTransaction", function (response) {
    socket.broadcast.emit("newTransaction", response);
  });
  socket.on("newUserState", function (response) {
    socket.broadcast.emit("newUserState", response);
  });
  socket.on("newMaxDebit", function (response) {
    socket.broadcast.emit("newMaxDebit", response);
  });
  socket.on("deleteAdmin", function (response) {
    socket.broadcast.emit("deleteAdmin", response);
  });
  socket.on("updatesOnDefaultCategory", function (response) {
    socket.broadcast.emit("updatesOnDefaultCategory", response);
  });
  socket.on("deleteUser", function (response) {
    socket.broadcast.emit("deleteUser", response);
  });
  socket.on("newUserCreated", function (response) {
    socket.broadcast.emit("newUserCreated", response);
  });
  socket.on("newAdminCreated", function (response) {
    socket.broadcast.emit("newAdminCreated", response);
  });
});
