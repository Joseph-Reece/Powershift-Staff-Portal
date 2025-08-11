import {
    start as startTurbo
} from '@hotwired/turbo';
import('./bootstrap');
import('./elements/turbo-echo-stream-tag');
import 'alpine-turbo-drive-adapter';
import 'alpinejs';
// import('tom-select');
window.TomSelect = require('tom-select');
startTurbo();
