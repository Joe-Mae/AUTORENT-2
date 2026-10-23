// Le CSS des icônes passe par JS (et non par le SASS) : c'est ce qui
// permet à Vite d'émettre les fichiers de police dans public/build.
import 'bootstrap-icons/font/bootstrap-icons.css';

import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;
