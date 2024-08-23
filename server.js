const express = require('express');
const app = express();
const http = require('http');
const server = http.createServer(app);

const io = require('socket.io')(server, {
  cors: {
    origin: '*',
  },
});


io.on('connection', (socket) => {
    console.log('a user connected');

    socket.on('chat', (msg) => {
        console.log('message: ' + msg);
        io.sockets.emit('chat', msg);
    });


    // seen event
    socket.on('seen', (msg) => {
        console.log('seen: ' + msg);
        io.sockets.emit('seen', msg);
    });

    // clear-chat
    socket.on('clear-chat', (msg) => {
        console.log('clear-chat: ' + msg);
        io.sockets.emit('clear-chat', msg);
    });

    // sendTeamMessage
    socket.on('sendTeamMessage', (msg) => {
        console.log('sendTeamMessage: ' + msg);
        io.sockets.emit('sendTeamMessage', msg);
    });
    //updateGroupImage1
    socket.on('updateGroupImage', (msg) => {
        console.log('updateGroupImage: ' + msg);
        io.sockets.emit('updateGroupImage', msg);
    });

    // removeMemberFromGroup
    socket.on('removeMemberFromGroup', (msg) => {
        console.log('removeMemberFromGroup: ' + msg);
        io.sockets.emit('removeMemberFromGroup', msg);
    });

    // exitFromGroup
    socket.on('exitFromGroup', (msg) => {
        console.log('exitFromGroup: ' + msg);
        io.sockets.emit('exitFromGroup', msg);
    });

    // addMemberToGroup
    socket.on('addMemberToGroup', (msg) => {
        console.log('addMemberToGroup: ' + msg);
        io.sockets.emit('addMemberToGroup', msg);
    });

    // createTeam
    socket.on('createTeam', (msg) => {
        console.log('createTeam: ' + msg);
        io.sockets.emit('createTeam', msg);
    });

    socket.on('disconnect', () => {
        console.log('user disconnected');
    });
});

server.listen(3000, () => {
    console.log('listening on *:3000');
});



