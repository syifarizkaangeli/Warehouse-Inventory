export async function onRequestGet({ env }) {
    try {
        const result = await env.DB
            .prepare(`
                SELECT
                    id,
                    nama_barang,
                    jumlah,
                    harga_per_pcs,
                    created_at,
                    updated_at
                FROM barang
                ORDER BY id DESC
            `)
            .all();

        return Response.json({
            success: true,
            data: result.results
        });

    } catch (error) {
        return Response.json({
            success: false,
            message: error.message
        }, { status: 500 });
    }
}


export async function onRequestPost({ env, request }) {
    try {
        const body = await request.json();

        const namaBarang =
            String(body.nama_barang || "").trim();

        const jumlah =
            Number(body.jumlah);

        const harga =
            Number(body.harga_per_pcs);

        if (!namaBarang) {
            return Response.json({
                success: false,
                message: "Nama barang wajib diisi."
            }, { status: 400 });
        }

        if (
            !Number.isInteger(jumlah) ||
            jumlah < 0
        ) {
            return Response.json({
                success: false,
                message: "Jumlah barang tidak valid."
            }, { status: 400 });
        }

        if (
            !Number.isFinite(harga) ||
            harga < 0
        ) {
            return Response.json({
                success: false,
                message: "Harga barang tidak valid."
            }, { status: 400 });
        }

        const result =
            await env.DB
                .prepare(`
                    INSERT INTO barang
                    (
                        nama_barang,
                        jumlah,
                        harga_per_pcs
                    )
                    VALUES (?, ?, ?)
                `)
                .bind(
                    namaBarang,
                    jumlah,
                    harga
                )
                .run();

        return Response.json({
            success: true,
            message: "Barang berhasil ditambahkan.",
            id: result.meta.last_row_id
        });

    } catch (error) {
        return Response.json({
            success: false,
            message: error.message
        }, { status: 500 });
    }
}


export async function onRequestPut({ env, request }) {
    try {
        const body = await request.json();

        const id =
            Number(body.id);

        const namaBarang =
            String(body.nama_barang || "").trim();

        const jumlah =
            Number(body.jumlah);

        const harga =
            Number(body.harga_per_pcs);

        if (!id) {
            return Response.json({
                success: false,
                message: "ID barang tidak valid."
            }, { status: 400 });
        }

        if (!namaBarang) {
            return Response.json({
                success: false,
                message: "Nama barang wajib diisi."
            }, { status: 400 });
        }

        if (
            !Number.isInteger(jumlah) ||
            jumlah < 0
        ) {
            return Response.json({
                success: false,
                message: "Jumlah barang tidak valid."
            }, { status: 400 });
        }

        if (
            !Number.isFinite(harga) ||
            harga < 0
        ) {
            return Response.json({
                success: false,
                message: "Harga barang tidak valid."
            }, { status: 400 });
        }

        const result =
            await env.DB
                .prepare(`
                    UPDATE barang
                    SET
                        nama_barang = ?,
                        jumlah = ?,
                        harga_per_pcs = ?,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                `)
                .bind(
                    namaBarang,
                    jumlah,
                    harga,
                    id
                )
                .run();

        if (result.meta.changes === 0) {
            return Response.json({
                success: false,
                message: "Barang tidak ditemukan."
            }, { status: 404 });
        }

        return Response.json({
            success: true,
            message: "Barang berhasil diperbarui."
        });

    } catch (error) {
        return Response.json({
            success: false,
            message: error.message
        }, { status: 500 });
    }
}


export async function onRequestDelete({ env, request }) {
    try {
        const url =
            new URL(request.url);

        const id =
            Number(
                url.searchParams.get("id")
            );

        if (!id) {
            return Response.json({
                success: false,
                message: "ID barang tidak valid."
            }, { status: 400 });
        }

        const result =
            await env.DB
                .prepare(`
                    DELETE FROM barang
                    WHERE id = ?
                `)
                .bind(id)
                .run();

        if (result.meta.changes === 0) {
            return Response.json({
                success: false,
                message: "Barang tidak ditemukan."
            }, { status: 404 });
        }

        return Response.json({
            success: true,
            message: "Barang berhasil dihapus."
        });

    } catch (error) {
        return Response.json({
            success: false,
            message: error.message
        }, { status: 500 });
    }
}