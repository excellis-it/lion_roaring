const express = require('express');
const app = express();
const http = require('http');
const server = http.createServer(app);

const io = require('socket.io')(server, {
  cors: {
    origin: '*',
  },
});

// const https = require('https');
// const fs = require('fs');

// const express = require('express');
// const app = express();

// // Load SSL certificate files
// const privateKey = fs.readFileSync('/etc/ssl/paidkey/key/dollarcare.org.key', 'utf8');
// const certificate = fs.readFileSync('/etc/ssl/paidkey/key/dollarcare_org.crt', 'utf8');
// const credentials = { key: privateKey, cert: certificate };

// const httpsServer = https.createServer(credentials, app);

// const io = require('socket.io')(httpsServer, {
//     cors: {
//         origin: '*',
//     },
// });


io.on('connection', (socket) => {
    console.log('a user connected');

    socket.on('chat', (msg) => {
        console.log('message: ' + msg);
        io.sockets.emit('chat', msg);
    });

    // remove-chat
    socket.on('remove-chat', (msg) => {
        console.log('remove-chat: ' + msg);
        io.sockets.emit('remove-chat', msg);
    });


    // seen event
    socket.on('seen', (messageId) => {
        console.log('message seen: ' + messageId);
        io.sockets.emit('seen', messageId);
    });

    // multiple_seen
    socket.on('multiple_seen', (msg) => {
        console.log('multiple_seen: ' + msg);
        io.sockets.emit('multiple_seen', msg);
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
    // deleteGroup
    socket.on('deleteGroup', (msg) => {
        console.log('deleteGroup: ' + msg);
        io.sockets.emit('deleteGroup', msg);
    });
    // team-remove-chat
    socket.on('team-remove-chat', (msg) => {
        console.log('team-remove-chat: ' + msg);
        io.sockets.emit('team-remove-chat', msg);
    });

    // sendAdminNotification
    socket.on('sendAdminNotification', (msg) => {
        console.log('sendAdminNotification: ' + msg);
        io.sockets.emit('sendAdminNotification', msg);
    });

    socket.on('disconnect', () => {
        console.log('user disconnected');
    });
});

server.listen(3000, () => {
    console.log('listening on *:3000');
});



