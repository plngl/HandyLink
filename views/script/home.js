document.getElementById('profileDropdownTrigger').addEventListener('click', () => {
     document.getElementById('profileDropdown').classList.toggle('hidden');
});

document.addEventListener('click', (e) => {
    const trigger = document.getElementById('profileDropdownTrigger');
    const dropdown = document.getElementById('profileDropdown');
    if (!trigger.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});