import './bootstrap';
import { createApp } from 'vue';
import axios from "axios";
import CrawlComponent from './components/CrawlComponent.vue';


const app = createApp({});

app.component('crawl-component', CrawlComponent);
app.mount('#app');
