import './bootstrap';
import { createApp } from 'vue';
import {openModal} from "jenesius-vue-modal";
import axios from "axios";

import Modal from './components/modals/ViewReportModalComponent.vue';
import CrawlComponent from './components/CrawlComponent.vue';

const app = createApp({});

app.component('modal-component', Modal);
app.component('crawl-component', CrawlComponent);


app.mount('#app');

