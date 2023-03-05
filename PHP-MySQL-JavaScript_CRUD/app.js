

document.querySelector('#handleSubmit').addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
        const item_name = await document.querySelector('#item_name').value;
        const item_price = await document.querySelector('#item_price').value;
        const payload = await { item_name: item_name, item_price: item_price }
        let response = await myapi.post('create_item', payload);
        await alert(response.message);
        await load_items();
        await resetForm();
    } catch (error) {
        if (!error?.response) {
            await console.error('No Server Response');
        } else {
            await console.error(error?.response?.message);
        }
    }
});

// <!-- //? Clicking 'Edit' button in the table fetches the item detail of the selected row -->
const get_item = async (id) => {
    document.querySelector('#row_id').value = await `Item ID : ${id}`;
    document.querySelector('#row_id').classList.remove('hide')
    document.querySelector('#update_btn').classList.remove('hide');
    document.querySelector('#add_btn').classList.add('hide')
    try {
        let response = await myapi.get(`item_detail&item=${id}`);
        document.querySelector('#item_name').value = await response?.data[0]?.item_name;
        document.querySelector('#item_price').value = await response?.data[0]?.item_price;
    } catch (error) {
        if (!error?.response) {
            await console.error('No Server Response');
        } else {
            await console.error(error?.response);
        }
    }
}
// <!-- //? Clicking the 'Update' button updates the selected row associeted with its id -->
const update_item = async () => {
    const id = document.querySelector('#row_id').value;
    const newid = parseInt(id.match(/\d+/)[0], 10);
    const payload = {
        row_id: newid,
        item_name: document.querySelector('#item_name').value,
        item_price: document.querySelector('#item_price').value,
    }
    const { row_id, item_name, item_price } = payload;
    try {
        let response = await myapi.put(`upate_item&row_id=${parseInt(row_id)}`, payload);
        response && load_items() && resetForm();
    } catch (error) {
        if (!error?.response) {
            console.error('No Server Response');
        } else {
            console.error(error?.response?.message);
        }
    }
}


// <!-- //? Clicking 'Delete' button will delete the selected row and be removed from the list -->
const delete_item = async (id) => {
    let reply = await myapi.delete(`delete_item&row_id=${id}`);
    await reply?.ok && alert('Item has been deleted!');
    await load_items();
}

const load_items = async () => {
    try {
        let response = await myapi.get('fetch_list');

        let tbody = document.querySelector('tbody#list');
        if (!tbody) {
            tbody = document.createElement('tbody#list');
            table.appendChild(tbody);
        } else {
            tbody.innerHTML = '';
        }

        let num = 1;
        response?.data.length !== 0 &&
        response.data.forEach(element => {
                // ? Basic DOM
                const row = document.createElement('tr');

                const numberOrderCell = document.createElement('td');
                numberOrderCell.textContent = num++;
                row.appendChild(numberOrderCell);

                const nameCell = document.createElement('td');
                nameCell.textContent = element.item_name;
                row.appendChild(nameCell);

                const priceCell = document.createElement('td');
                priceCell.textContent = element.item_price;
                row.appendChild(priceCell);

                const actionsCell = document.createElement('td');
                actionsCell.innerHTML =
                    `<button type="button" onclick="get_item(${element.row_id})">Edit</button>
             <button type="button" onclick="delete_item(${element.row_id})">Delete</button>`;
                row.appendChild(actionsCell);

                tbody.appendChild(row)
            });

        function seterror() {
            const row = document.createElement('tr');
            const emptycell = document.createElement('td');
            emptycell.setAttribute('id', 'error_list');
            emptycell.setAttribute('colspan', 4);
            emptycell.textContent = response?.message;
            row.appendChild(emptycell);
            tbody.appendChild(row);
        }
        !response?.data.length && seterror();
    } catch (error) {
        if (!error?.response) {
            console.error('No Server Response');
        } else {
            console.error(!error?.response);
        }
    }
}

// <!-- //? Resets the form and all input values -->
const resetForm = () => {
    document.querySelector('#row_id').classList.add('hide')
    document.querySelector('#update_btn').classList.add('hide');
    document.querySelector('#add_btn').classList.remove('hide')
    document.querySelector('#row_id').value = "";
    document.querySelector('#item_name').value = "";
    document.querySelector('#item_price').value = "";
}
// <!-- //? Load items upon refresh or when opening the webpage -->
setTimeout(() => {
    load_items();
}, 100);
