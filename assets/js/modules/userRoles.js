/**
 * Functionality for adding/removing custom user role settings field.
 * Basically imitates feel of a repeater settings field.
 */
 const userRoles = () => {
    const add = document.querySelector('.nfc-user-roles-add');

    if (!add) {
        return;
    }

    add.addEventListener('click', () => {
        const fields = document.querySelectorAll('.nfc-user-roles-field');
        const field = fields[fields.length - 1];

        if (field) {
            const clone = field.cloneNode(true);

            const input = clone.querySelector('input[type="text"]');
            const inputRoleKey = input.getAttribute('data-role-key');

            input.setAttribute('name', `nfc_events_setting_user_roles[${parseInt(inputRoleKey) + 1}][name]`);
            input.setAttribute('data-role-key', parseInt(inputRoleKey) + 1);
            input.value = '';

            const select = clone.querySelector('select');
            const selectRoleKey = select.getAttribute('data-role-key');

            select.setAttribute('name', `nfc_events_setting_user_roles[${parseInt(selectRoleKey) + 1}][cap]`);
            select.setAttribute('data-role-key', parseInt(selectRoleKey) + 1);
            select.value = '';

            const archived = clone.querySelector('input[type="checkbox"]');
            
            archived.setAttribute('name', `nfc_events_setting_user_roles[${parseInt(selectRoleKey) + 1}][archived]`);
            archived.setAttribute('data-role-key', parseInt(selectRoleKey) + 1);
            archived.setAttribute('id', `nfc_events_archive_role_${parseInt(selectRoleKey) + 1}`);
            archived.parentNode.querySelector('label').setAttribute('for', `nfc_events_archive_role_${parseInt(selectRoleKey) + 1}`);
            archived.checked = false;
            clone.classList.remove('--archived');

            field.parentNode.appendChild(clone);
        }
    });

    document.addEventListener('click', (e) => {
		if (e.target.classList.contains('nfc-user-roles-remove')) {
            const fields = document.querySelectorAll('.nfc-user-roles-field');
            const parent = e.target.parentNode;

            if (fields.length === 1) {
                parent.querySelector('input').value = '';
                parent.querySelector('select').value = '';
            } else {
                parent.remove();
            }
        } else if (e.target.parentNode.parentNode.classList.contains('nfc-user-roles-archive')) {
            if (e.target.closest('.nfc-user-roles-field').classList.contains('--archived')) {
                e.target.closest('.nfc-user-roles-field').classList.remove('--archived');
            } else {
                e.target.closest('.nfc-user-roles-field').classList.add('--archived');
            }
        }
	});
}

export {
	userRoles,
}
