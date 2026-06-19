const toggleButton = document.getElementById('toggle-btn');
const sidebar = document.getElementById('sidebar');

function toggleSidebar() {
    sidebar.classList.toggle('close');

    Array.from(sidebar.getElementsByClassName('show')).forEach(uL => {
        uL.classList.remove('show');
        uL.previousElementSibling.classList.remove('rotate');
    })
}

function toggleSubMenu(button) {
    button.nextElementSibling.classList.toggle('show')
    button.classList.toggle('rotate');

    if (sidebar.classList.contains('close')) {
        sidebar.classList.toggle('close');
    }
}