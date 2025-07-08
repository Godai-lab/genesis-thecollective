import './bootstrap';

// import Alpine from 'alpinejs';

import {Alpine,Livewire} from '../../vendor/livewire/livewire/dist/livewire.esm';
import ToastComponent from '../../vendor/usernotnull/tall-toasts/resources/js/tall-toasts';
import Swal from 'sweetalert2';

Alpine.plugin(ToastComponent);

// Alpine.start();

Livewire.start();

// window.Alpine = Alpine;

window.Swal = Swal;




