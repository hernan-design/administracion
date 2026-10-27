import './components/main.jsx';
import.meta.glob('./models/*.js', { eager: true });
import.meta.glob('./collections/*.js', { eager: true });
import.meta.glob('./views/**/*.js', { eager: true });
import.meta.glob('./routers/*.js', { eager: true });
import './init.js';
