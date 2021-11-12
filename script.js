

function phoneMask(e) {
    let val = e.target.value.replace(/\D/g, '')

    if (val) {
        if (val[0] === '7' || val[0] === '8') {
            val = val.slice(1)
        }

        val = val.match(/(\d{0,3})(\d{0,3})(\d{0,2})(\d{0,2})/)
        val = '+7' + (val[2] ? ' (' + val[1] + ') ' + val[2] : val[1] ? val[1] : '') + (val[3] ? '-' + val[3] : '') + (val[4] ? '-' + val[4] : '')
    }

    e.target.value = val
}

function inputTelHandler() {
    const tel = document.querySelector('input[type="tel"]');
    tel.addEventListener('input', (e) => {
        phoneMask(e);

        tel.classList.remove('error');
        if (e.target.value.length === 18) {
            tel.classList.add('success');
        } else {
            tel.classList.remove('success');
        }
    });
}

function popupHandler() {
    const popup = document.querySelector('.popup');
    const form = document.querySelector('.form-popup');

    const body = document.body;

    body.addEventListener('click', (e) => {

        if (e.target.closest('.click-btn')) {
            popup.classList.add('active');
            form.classList.add('active');
        } else if (e.target.closest('.popup__close') || !e.target.closest('.popup__wrapper')) {
            popupClose();
        }
    });
}

function popupClose() {
    document.querySelector('.popup').classList.remove('active');
    document.querySelector('.form-popup').classList.remove('active');
    document.querySelector('.success-window').classList.remove('active');
    document.querySelector('.fail-window').classList.remove('active');

}
function openCloseSuccess(type, text = 'open') {
    if (text === 'open') {
        document.querySelector('.popup').classList.add('active');
        document.querySelector(`.${type}-window`).classList.add('active');
    }
    else {
        popupClose();
    }
}
function formHandler() {
    const form = document.querySelector('form');
    const tel = document.querySelector('input[type="tel"]');


    form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (tel.classList.contains('success')) {
            data = new FormData(e.target);
            fetch('send.php', {
                method: 'POST',
                body: data
            }).then(response => {
                if (response.status === 200) {

                    cleanFields();
                    popupClose();
                    openCloseSuccess('success', 'open');
                    setTimeout(() => { openCloseSuccess('success', 'close') }, 3000);
                    console.log('success');
                } else {
                    cleanFields();
                    popupClose();
                    openCloseSuccess('fail', 'open');
                    setTimeout(() => { openCloseSuccess('fail', 'close') }, 3000);
                    console.log('fail');
                }
            });
        } else {
            tel.classList.add('error');
        }

    });
}

function cleanFields() {
    const inputs = document.querySelectorAll('input');

    inputs.forEach(item => {
        item.value = '';
    });
}
document.addEventListener('DOMContentLoaded', () => {
    inputTelHandler();
    popupHandler();
    formHandler();
});