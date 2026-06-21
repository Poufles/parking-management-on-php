const elError = document.querySelectorAll('section.error');

if (elError) {
    elError.forEach(error => {
        const closeButton = error.querySelector('#close');

        if (closeButton) {
            closeButton.addEventListener('click', e => {
                const parentEl = error.parentElement;

                parentEl.removeChild(error);
            })
        }
    }); 
}