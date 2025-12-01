$(function () {

    const baseUrl = $('meta[name="baseUrl"]').attr('content');

    let selectedProducts = [];
    let selectedProduct = null;
    let customerDetails = {};

    $('#customer_email').on('blur', function () {
        const email = $(this).val().trim();
        if (!email) return;

        axios.get(`${baseUrl}/customers/get/customer`, { params: { query: email } })
            .then(res => {
                if (res.data.success && res.data.data) {
                    $('#customer_name').val(res.data.data.name).prop('readonly', true);
                    customerDetails = {
                        name: res.data.data.name,
                        email: res.data.data.email
                    };
                } else {
                    $('#customer_name').val('').prop('readonly', false);
                    customerDetails = {};
                }
            })
            .catch(err => {
                console.error('Customer lookup error', err);
                $('#customer_name').val('').prop('readonly', false);
                customerDetails = {};
            });
    });

    $('#product_code').on('keyup', function () {
        const q = $(this).val().trim();

        if (q.length < 2) {
            $('#product_suggestions').addClass('hidden').empty();
            return;
        }

        axios.get(`${baseUrl}/products/all/products`, { params: { query: q } })
            .then(res => {
                const list = $('#product_suggestions').empty().removeClass('hidden');
                const items = res.data?.data ?? [];

                if (!items.length) {
                    list.append(`<li class="p-2 text-gray-500">No results</li>`);
                    return;
                }

                items.forEach(p => {
                    // ensure product object has price and tax_percentage
                    list.append(`
                        <li class="p-2 hover:bg-gray-100 cursor-pointer"
                            data-product='${JSON.stringify(p)}'>
                            ${p.product_code} - ${p.name}
                        </li>
                    `);
                });
            })
            .catch(err => {
                console.error('Product search error', err);
                $('#product_suggestions').addClass('hidden').empty();
            });
    });

    // suggestion click
    $(document).on('click', '#product_suggestions li', function () {
        const raw = $(this).attr('data-product');
        try {
            selectedProduct = JSON.parse(raw);
        } catch (e) {
            console.error('Invalid product JSON', e);
            selectedProduct = null;
            return;
        }
        $('#product_code').val(selectedProduct.product_code);
        $('#product_suggestions').addClass('hidden').empty();
    });

    $('#addProductForm').submit(function (e) {
        e.preventDefault();

        const qty = parseInt($('#quantity').val(), 10);
        if (!selectedProduct || !(selectedProduct.id) || qty < 1) {
            alert('Select valid product and quantity');
            return;
        }

        // Normalize property name to tax_percentage
        const tax_percentage = parseFloat(selectedProduct.tax_percentage ?? selectedProduct.tax_slab ?? 0);

        const existing = selectedProducts.find(p => p.id === selectedProduct.id);
        if (existing) {
            existing.quantity += qty;
        } else {
            selectedProducts.push({
                id: selectedProduct.id,
                product_code: selectedProduct.product_code,
                name: selectedProduct.name,
                price: parseFloat(selectedProduct.price),
                tax_percentage: tax_percentage,
                quantity: qty
            });
        }

        renderTable();
        resetModal();
    });

    function resetModal() {
        $('#product_code').val('');
        $('#quantity').val(1);
        selectedProduct = null;
        $('#product_suggestions').addClass('hidden').empty();
        $('#addProductModal').addClass('hidden');
    }

    function renderTable() {
        let grandTotal = 0;
        const tbody = $('#productsTableBody').empty();

        if (!selectedProducts.length) {
            tbody.append($('#noProductsRow').removeClass('hidden'));
            $('#grandTotal').text('0.00');
            // also clear summary
            $('#total_without_tax').text('0.00');
            $('#total_tax').text('0.00');
            $('#net_total').text('0.00');
            $('#rounded_total').text('0.00');
            $('#balance').text('—');
            $('#denomination').html('');
            return;
        }

        $('#noProductsRow').addClass('hidden');

        selectedProducts.forEach((p, i) => {
            const purchase = (Number(p.price) || 0) * (Number(p.quantity) || 0);
            const tax = (purchase * (Number(p.tax_percentage) || 0)) / 100;
            const total = purchase + tax;

            grandTotal += total;

            tbody.append(`
              <tr>
                <td class="px-4 py-2">${escapeHtml(p.product_code)} - ${escapeHtml(p.name)}</td>
                <td class="px-4 py-2 text-right">₹ ${Number(p.price).toFixed(2)}</td>
                <td class="px-4 py-2 text-center w-32">
                  <input type="number"
                    min="1"
                    value="${p.quantity}"
                    class="qty-input w-20 border rounded px-2 py-1 text-center"
                    data-index="${i}">
                </td>
                <td class="px-4 py-2 text-right">₹ ${purchase.toFixed(2)}</td>
                <td class="px-4 py-2 text-right">₹ ${tax.toFixed(2)}</td>
                <td class="px-4 py-2 text-right font-semibold">₹ ${total.toFixed(2)}</td>
                <td class="px-4 py-2 text-center">
                  <button class="remove text-red-600" data-index="${i}">✕</button>
                </td>
              </tr>
            `);
        });

        $('#grandTotal').text(grandTotal.toFixed(2));
        calculateSummary(); // update the summary area
    }

    // small helper to avoid HTML injection in product names
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    $(document).on('input change', '.qty-input', function () {
        const i = Number($(this).data('index'));
        let v = parseInt($(this).val(), 10);
        if (!v || v < 1) {
            v = 1;
            $(this).val(1);
        }
        selectedProducts[i].quantity = v;
        renderTable();
    });

    // remove row
    $(document).on('click', '.remove', function () {
        selectedProducts.splice($(this).data('index'), 1);
        renderTable();
    });

    function calculateSummary() {
        let withoutTax = 0;
        let tax = 0;

        selectedProducts.forEach(p => {
            const purchase = (Number(p.price) || 0) * (Number(p.quantity) || 0);
            const t = (purchase * (Number(p.tax_percentage) || 0)) / 100;
            withoutTax += purchase;
            tax += t;
        });

        const net = withoutTax + tax;
        const rounded = Math.round(net);

        $('#total_without_tax').text(withoutTax.toFixed(2));
        $('#total_tax').text(tax.toFixed(2));
        $('#net_total').text(net.toFixed(2));
        $('#rounded_total').text(rounded.toFixed(2));

        calculateBalance(); // recalc balance if cash input exists
    }

    $('#cash_paid').on('input', calculateBalance);

    function calculateBalance() {
        const paid = parseFloat($('#cash_paid').val()) || 0;
        const total = parseFloat($('#rounded_total').text()) || 0;

        if (paid < total || total === 0) {
            $('#balance').text('—');
            $('#denomination').html('');
            return;
        }

        const balance = paid - total;
        $('#balance').text(balance.toFixed(2));
        showDenomination(balance);
    }

    function showDenomination(amount) {
        let remaining = Math.floor(amount); // use integer rupees for denominations
        const notes = [2000, 500, 200, 100, 50, 20, 10, 5, 2, 1];
        let html = '';

        notes.forEach(note => {
            const count = Math.floor(remaining / note);
            if (count > 0) {
                html += `<div class="flex justify-between"><span>${note} :</span><span>${count}</span></div>`;
                remaining = remaining % note;
            }
        });

        $('#denomination').html(html);
    }

    $('#addProductBtn').on('click', function () {
        $('#addProductModal').removeClass('hidden');
        $('#product_code').val('');
        $('#product_suggestions').addClass('hidden').empty();
        $('#quantity').val(1);
    });

    $('#cancelAddProduct').on('click', resetModal);


    $('#generateBillBtn').on('click', function () {
        if (!selectedProducts.length) {
            alert('Add at least one product!');
            return;
        }

        const email = $('#customer_email').val().trim();
        const name = $('#customer_name').val().trim();
        if (!email || !name) {
            alert('Please enter the customer details!');
            return;
        }

        const paid = parseFloat($('#cash_paid').val()) || 0;
        const total = parseFloat($('#rounded_total').text()) || 0;

        if (paid < total) {
            alert('Cash paid must be greater than or equal to bill amount');
            return;
        }

        const payload = {
            customer_email: email,
            customer_name: name,
            cash_paid: paid,
            products: selectedProducts.map(p => ({
                id: p.id,
                quantity: p.quantity
            }))
        };

        axios.post(`${baseUrl}/billings/create`, payload)
            .then(res => {
                if (res.status === 200) {
                    window.open(res.data.data.url_to_navigate, '_blank');
                }else {
                    alert('Invoice created, but no preview link returned.');
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error('Create bill error:', err);
                alert(err.response?.data?.message || 'Error generating bill');
            });
    });
    renderTable();
});
