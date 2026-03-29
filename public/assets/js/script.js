let rowIndex = document.querySelectorAll('#tbody tr').length;

const addItem = () => {
    let tbody = document.getElementById("tbody");
    tbody.insertAdjacentHTML(
        "beforeend",
        `
        <tr>
            <td><input type="text" name="items[${rowIndex}][item_name]" class="form-control"></td>
            <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty" min="1"></td>
            <td><input type="number" name="items[${rowIndex}][price]" class="form-control price" min="0" step="0.01"></td>
            <td><input type="text" name="items[${rowIndex}][sub_total]" class="form-control sub-total" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        </tr>
    `,
    );
    rowIndex++;
    updateDeleteButtons();
};

const removeItem = (btn) => {
    btn.closest("tr").remove();
    calcTotal();
    updateDeleteButtons();
};

const updateDeleteButtons = () => {
    let rows = document.querySelectorAll("#tbody tr");
    rows.forEach((row) => {
        row.querySelector("button").disabled = rows.length === 1;
    });
};

const calcTotal = () => {
    let total = 0;
    document.querySelectorAll(".sub-total").forEach((el) => {
        total += parseFloat(el.value) || 0;
    });
    document.getElementById("total_amount").value = total.toFixed(2);
};

document.getElementById("tbody").addEventListener("input", (e) => {
    let row = e.target.closest("tr");
    let qty = row.querySelector(".qty").value || 0;
    let price = row.querySelector(".price").value || 0;
    row.querySelector(".sub-total").value = (qty * price).toFixed(2);
    calcTotal();
});
