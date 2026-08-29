const API_URL = "/api/barang";

let semuaBarang = [];
let editMode = false;

document.addEventListener("DOMContentLoaded", () => {
    initPage();
});

async function initPage() {
    const barangTable = document.getElementById("barangTable");
    const laporanTable = document.getElementById("laporanTable");

    if (barangTable) {
        await loadBarang();

        const searchInput =
            document.getElementById("searchInput");

        if (searchInput) {
            searchInput.addEventListener("input", () => {
                renderBarang(searchInput.value);
            });
        }

        const form =
            document.getElementById("barangForm");

        if (form) {
            form.addEventListener(
                "submit",
                saveBarang
            );
        }
    }

    if (laporanTable) {
        await loadLaporan();
    }
}

async function request(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            "Content-Type": "application/json",
            ...(options.headers || {})
        }
    });

    let data;

    try {
        data = await response.json();
    } catch {
        throw new Error("Response server tidak valid.");
    }

    if (!response.ok || data.success === false) {
        throw new Error(
            data.message || "Terjadi kesalahan."
        );
    }

    return data;
}

async function loadBarang() {
    const table =
        document.getElementById("barangTable");

    if (!table) {
        return;
    }

    try {
        const data = await request(API_URL);

        semuaBarang = data.data || [];

        renderBarang();

        updateStats();

    } catch (error) {
        table.innerHTML = `
            <tr>
                <td colspan="6" class="error">
                    ${escapeHTML(error.message)}
                </td>
            </tr>
        `;
    }
}

function renderBarang(keyword = "") {
    const table =
        document.getElementById("barangTable");

    if (!table) {
        return;
    }

    const search =
        keyword.toLowerCase().trim();

    const filtered = semuaBarang.filter(barang =>
        String(barang.nama_barang)
            .toLowerCase()
            .includes(search)
    );

    if (filtered.length === 0) {
        table.innerHTML = `
            <tr>
                <td colspan="6" class="empty">
                    Belum ada barang.
                </td>
            </tr>
        `;

        return;
    }

    table.innerHTML = filtered.map(
        (barang, index) => {

            const total =
                Number(barang.jumlah) *
                Number(barang.harga_per_pcs);

            return `
                <tr>

                    <td>${index + 1}</td>

                    <td>
                        ${escapeHTML(
                            barang.nama_barang
                        )}
                    </td>

                    <td>
                        ${formatNumber(
                            barang.jumlah
                        )}
                    </td>

                    <td>
                        ${formatRupiah(
                            barang.harga_per_pcs
                        )}
                    </td>

                    <td>
                        ${formatRupiah(total)}
                    </td>

                    <td class="actions">

                        <button
                            class="btn-edit"
                            onclick="editBarang(${barang.id})"
                        >
                            Edit
                        </button>

                        <button
                            class="btn-delete"
                            onclick="deleteBarang(${barang.id})"
                        >
                            Hapus
                        </button>

                    </td>

                </tr>
            `;
        }
    ).join("");
}

function updateStats() {
    const totalBarang =
        semuaBarang.length;

    const totalStok =
        semuaBarang.reduce(
            (total, barang) =>
                total + Number(barang.jumlah || 0),
            0
        );

    const totalNilai =
        semuaBarang.reduce(
            (total, barang) =>
                total +
                (
                    Number(barang.jumlah || 0) *
                    Number(barang.harga_per_pcs || 0)
                ),
            0
        );

    const elementBarang =
        document.getElementById("totalBarang");

    const elementStok =
        document.getElementById("totalStok");

    const elementNilai =
        document.getElementById("totalNilai");

    if (elementBarang) {
        elementBarang.textContent =
            formatNumber(totalBarang);
    }

    if (elementStok) {
        elementStok.textContent =
            formatNumber(totalStok);
    }

    if (elementNilai) {
        elementNilai.textContent =
            formatRupiah(totalNilai);
    }
}

function openModal() {
    const modal =
        document.getElementById("barangModal");

    const title =
        document.getElementById("modalTitle");

    const form =
        document.getElementById("barangForm");

    if (!modal) {
        return;
    }

    editMode = false;

    if (title) {
        title.textContent = "Tambah Barang";
    }

    if (form) {
        form.reset();
    }

    const id =
        document.getElementById("barangId");

    if (id) {
        id.value = "";
    }

    modal.classList.add("show");
}

function closeModal() {
    const modal =
        document.getElementById("barangModal");

    if (modal) {
        modal.classList.remove("show");
    }
}

function editBarang(id) {
    const barang =
        semuaBarang.find(
            item => Number(item.id) === Number(id)
        );

    if (!barang) {
        showToast(
            "Data barang tidak ditemukan.",
            "error"
        );

        return;
    }

    editMode = true;

    document.getElementById("barangId").value =
        barang.id;

    document.getElementById("namaBarang").value =
        barang.nama_barang;

    document.getElementById("jumlahBarang").value =
        barang.jumlah;

    document.getElementById("hargaBarang").value =
        barang.harga_per_pcs;

    document.getElementById("modalTitle").textContent =
        "Edit Barang";

    document
        .getElementById("barangModal")
        .classList.add("show");
}

async function saveBarang(event) {
    event.preventDefault();

    const id =
        document.getElementById("barangId").value;

    const nama =
        document
            .getElementById("namaBarang")
            .value
            .trim();

    const jumlah =
        Number(
            document
                .getElementById("jumlahBarang")
                .value
        );

    const harga =
        Number(
            document
                .getElementById("hargaBarang")
                .value
        );

    if (!nama) {
        showToast(
            "Nama barang wajib diisi.",
            "error"
        );

        return;
    }

    if (jumlah < 0 || !Number.isInteger(jumlah)) {
        showToast(
            "Jumlah barang tidak valid.",
            "error"
        );

        return;
    }

    if (harga < 0 || !Number.isFinite(harga)) {
        showToast(
            "Harga barang tidak valid.",
            "error"
        );

        return;
    }

    try {
        const data = {
            nama_barang: nama,
            jumlah: jumlah,
            harga_per_pcs: harga
        };

        if (editMode) {
            data.id = Number(id);

            await request(API_URL, {
                method: "PUT",
                body: JSON.stringify(data)
            });

            showToast(
                "Barang berhasil diperbarui."
            );

        } else {
            await request(API_URL, {
                method: "POST",
                body: JSON.stringify(data)
            });

            showToast(
                "Barang berhasil ditambahkan."
            );
        }

        closeModal();

        await loadBarang();

    } catch (error) {
        showToast(
            error.message,
            "error"
        );
    }
}

async function deleteBarang(id) {
    const barang =
        semuaBarang.find(
            item => Number(item.id) === Number(id)
        );

    if (!barang) {
        return;
    }

    const yakin = confirm(
        `Hapus barang "${barang.nama_barang}"?`
    );

    if (!yakin) {
        return;
    }

    try {
        await request(
            `${API_URL}?id=${id}`,
            {
                method: "DELETE"
            }
        );

        showToast(
            "Barang berhasil dihapus."
        );

        await loadBarang();

    } catch (error) {
        showToast(
            error.message,
            "error"
        );
    }
}

async function loadLaporan() {
    try {
        const data =
            await request(API_URL);

        const barang =
            data.data || [];

        renderLaporan(barang);

    } catch (error) {

        const table =
            document.getElementById(
                "laporanTable"
            );

        if (table) {
            table.innerHTML = `
                <tr>
                    <td colspan="5" class="error">
                        ${escapeHTML(
                            error.message
                        )}
                    </td>
                </tr>
            `;
        }
    }
}

function renderLaporan(barang) {
    const table =
        document.getElementById(
            "laporanTable"
        );

    if (!table) {
        return;
    }

    let totalStok = 0;
    let totalNilai = 0;

    if (barang.length === 0) {

        table.innerHTML = `
            <tr>
                <td colspan="5" class="empty">
                    Belum ada data barang.
                </td>
            </tr>
        `;

    } else {

        table.innerHTML = barang.map(
            (item, index) => {

                const jumlah =
                    Number(item.jumlah || 0);

                const harga =
                    Number(
                        item.harga_per_pcs || 0
                    );

                const nilai =
                    jumlah * harga;

                totalStok += jumlah;
                totalNilai += nilai;

                return `
                    <tr>

                        <td>
                            ${index + 1}
                        </td>

                        <td>
                            ${escapeHTML(
                                item.nama_barang
                            )}
                        </td>

                        <td>
                            ${formatNumber(
                                jumlah
                            )}
                        </td>

                        <td>
                            ${formatRupiah(
                                harga
                            )}
                        </td>

                        <td>
                            ${formatRupiah(
                                nilai
                            )}
                        </td>

                    </tr>
                `;
            }
        ).join("");
    }

    const totalBarang =
        document.getElementById(
            "laporanTotalBarang"
        );

    const totalStokElement =
        document.getElementById(
            "laporanTotalStok"
        );

    const totalNilaiElement =
        document.getElementById(
            "laporanTotalNilai"
        );

    if (totalBarang) {
        totalBarang.textContent =
            formatNumber(barang.length);
    }

    if (totalStokElement) {
        totalStokElement.textContent =
            formatNumber(totalStok);
    }

    if (totalNilaiElement) {
        totalNilaiElement.textContent =
            formatRupiah(totalNilai);
    }
}

function formatRupiah(value) {
    return new Intl.NumberFormat(
        "id-ID",
        {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        }
    ).format(Number(value) || 0);
}

function formatNumber(value) {
    return new Intl.NumberFormat(
        "id-ID"
    ).format(Number(value) || 0);
}

function escapeHTML(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function showToast(message, type = "success") {
    const toast =
        document.getElementById("toast");

    if (!toast) {
        alert(message);
        return;
    }

    toast.textContent = message;

    toast.className =
        `toast show ${type}`;

    setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}

window.openModal = openModal;
window.closeModal = closeModal;
window.editBarang = editBarang;
window.deleteBarang = deleteBarang;