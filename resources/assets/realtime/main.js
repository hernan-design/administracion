// file: realtime/main.js
import io from 'socket.io-client';
import '../js/helpers/misc.js';
const Misc = window.Misc;

import config from '../config/config.json';

// socket.io client
// const ioNotify = io.connect(`${window.location.host}:${config.port}/nsp-notify`, { 'forceNew': true });
const ioNotify = io.connect(`${window.document.platform}:${config.port}/nsp-notify`, { 'forceNew': true });

// Initialize attributes
const userid = Misc.getCookie('_user_id');

// Listen connect client
ioNotify.on('connect',() => {
	ioNotify.emit('checkuser', {id: userid});
});