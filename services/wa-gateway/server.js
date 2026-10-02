import express from 'express';
import cors from 'cors';

const app = express();
const PORT = process.env.PORT || 3001;

app.use(cors());
app.use(express.json());

// In-memory message logs buffer (max 100 entries)
const messageLogs = [];
let sentCount = 0;

/**
 * Normalize Indonesian phone numbers to standard format (e.g., 628123456789)
 */
function normalizePhone(phone) {
    if (!phone) return '';
    let cleaned = phone.toString().replace(/[^0-9]/g, '');
    if (cleaned.startsWith('0')) {
        cleaned = '62' + cleaned.substring(1);
    } else if (cleaned.startsWith('8')) {
        cleaned = '62' + cleaned;
    }
    return cleaned;
}

/**
 * Format official RuangWarga WhatsApp message header & footer
 */
function formatMessage(rawMessage, type = 'info') {
    const icons = {
        surat: '📄 *[LAYANAN SURAT RUANGWARGA]*',
        iuran: '💳 *[TAGIHAN IURAN KAS RT]*',
        pembayaran: '✅ *[KONFIRMASI PEMBAYARAN KAS RT]*',
        agenda: '🗓️ *[AGENDA & KEGIATAN WARGA]*',
        darurat: '🚨 *[PERINGATAN SIAGA LINGKUNGAN]*',
        aspirasi: '💬 *[TANGGAPAN ASPIRASI RT]*',
        info: '📢 *[INFORMASI RESMI RT/RW]*'
    };

    const header = icons[type] || icons.info;
    const footer = `\n--------------------------------\n_Pesan otomatis dari Sistem Informasi RuangWarga RT 05 / RW 01 Harmoni Residence._\n_Mohon tidak membalas pesan bot ini._`;

    return `${header}\n\n${rawMessage}\n${footer}`;
}

// 1. Healthcheck & Gateway Status
app.get('/health', (req, res) => {
    res.json({
        status: 'online',
        service: 'RuangWarga Node.js WA Gateway',
        version: '1.0.0',
        port: PORT,
        uptime_seconds: Math.floor(process.uptime()),
        total_dispatched: sentCount,
        timestamp: new Date().toISOString()
    });
});

app.get('/api/status', (req, res) => {
    res.json({
        success: true,
        gateway: 'RuangWarga WhatsApp Notification Gateway',
        status: 'CONNECTED',
        driver: 'simulation_ready',
        messages_sent: sentCount,
        recent_logs_count: messageLogs.length
    });
});

// 2. Direct Send Message API
app.post('/api/send-message', (req, res) => {
    const { phone, message, type = 'info', metadata = {} } = req.body;

    if (!phone || !message) {
        return res.status(400).json({
            success: false,
            message: 'Parameter phone dan message wajib diisi.'
        });
    }

    const formattedPhone = normalizePhone(phone);
    const finalContent = formatMessage(message, type);
    const messageId = `WA-MSG-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
    const now = new Date();

    const logEntry = {
        id: messageId,
        recipient_raw: phone,
        recipient_wa: formattedPhone,
        type: type,
        message_snippet: message.substring(0, 80) + (message.length > 80 ? '...' : ''),
        full_text: finalContent,
        status: 'DELIVERED',
        sent_at: now.toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }),
        metadata: metadata
    };

    messageLogs.unshift(logEntry);
    if (messageLogs.length > 100) messageLogs.pop();
    sentCount++;

    console.log(`[WA-GATEWAY] [${logEntry.sent_at}] ${type.toUpperCase()} -> ${formattedPhone} (ID: ${messageId})`);

    return res.json({
        success: true,
        message: 'Pesan WhatsApp berhasil dikirim / disimulasikan.',
        data: {
            messageId,
            recipient: formattedPhone,
            type,
            status: 'DELIVERED',
            sentAt: logEntry.sent_at
        }
    });
});

// 3. Pre-defined Templates API
app.post('/api/send-template', (req, res) => {
    const { template, phone, data = {} } = req.body;

    if (!template || !phone) {
        return res.status(400).json({
            success: false,
            message: 'Parameter template dan phone wajib diisi.'
        });
    }

    let message = '';
    let type = 'info';

    switch (template) {
        case 'surat_disetujui':
            type = 'surat';
            message = `Halo Bpk/Ibu *${data.nama || 'Warga'}*,\n\nPermohonan *${data.jenis_surat || 'Surat Pengantar RT'}* Anda (No. Tiket: *${data.ticket_code || '-'}*) telah *DISETUJUI & DITANDATANGANI* oleh Ketua RT.\n\nAnda dapat mengunduh dokumen surat resmi ber-QR Code langsung dari Portal Mandiri:\n🔗 ${data.download_url || 'http://localhost:8000/portal/layanan'}`;
            break;

        case 'tagihan_iuran':
            type = 'iuran';
            message = `Halo Bpk/Ibu *${data.nama || 'Warga'}*,\n\nTagihan Iuran Kas Lingkungan telah terbit:\n• *Nama Tagihan:* ${data.nama_tagihan || 'Iuran Bulanan RT'}\n• *Nominal:* Rp ${Number(data.nominal || 0).toLocaleString('id-ID')}\n• *Jatuh Tempo:* ${data.jatuh_tempo || '-'}\n\nSilakan lakukan transfer ke Rekening Resmi RT (*BCA 8820-9182-33*) atau scan QRIS melalui Portal Mandiri Warga:\n🔗 ${data.portal_url || 'http://localhost:8000/portal/layanan'}`;
            break;

        case 'pembayaran_lunas':
            type = 'pembayaran';
            message = `Halo Bpk/Ibu *${data.nama || 'Warga'}*,\n\nTerima kasih! Pembayaran tagihan *${data.nama_tagihan || 'Iuran RT'}* sebesar *Rp ${Number(data.nominal || 0).toLocaleString('id-ID')}* telah *DIVERIFIKASI LUNAS* oleh Bendahara RT.\n\nCatatan mutasi sah telah tercatat di Buku Kas RT.`;
            break;

        case 'jadwal_ronda':
            type = 'agenda';
            message = `Halo Bpk/Ibu *${data.nama || 'Warga'}*,\n\nPengingat jadwal *Ronda Malam Siskamling*:\n• *Hari/Tanggal:* ${data.hari || 'Hari Ini'}\n• *Jam:* 22.00 - 04.00 WIB\n• *Lokasi Kumpul:* Pos Ronda Utama RT 05\n\nMari bersama menjaga keamanan dan ketentraman lingkungan kita.`;
            break;

        default:
            return res.status(400).json({
                success: false,
                message: `Template '${template}' tidak dikenali. Pilihan: surat_disetujui, tagihan_iuran, pembayaran_lunas, jadwal_ronda.`
            });
    }

    const formattedPhone = normalizePhone(phone);
    const finalContent = formatMessage(message, type);
    const messageId = `WA-TPL-${Date.now()}-${Math.floor(Math.random() * 1000)}`;
    const now = new Date();

    const logEntry = {
        id: messageId,
        recipient_raw: phone,
        recipient_wa: formattedPhone,
        type: type,
        template: template,
        message_snippet: message.substring(0, 80) + '...',
        full_text: finalContent,
        status: 'DELIVERED',
        sent_at: now.toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' })
    };

    messageLogs.unshift(logEntry);
    if (messageLogs.length > 100) messageLogs.pop();
    sentCount++;

    console.log(`[WA-GATEWAY] [${logEntry.sent_at}] TEMPLATE (${template}) -> ${formattedPhone}`);

    return res.json({
        success: true,
        message: `Pesan WhatsApp template '${template}' berhasil dikirim.`,
        data: {
            messageId,
            recipient: formattedPhone,
            template,
            sentAt: logEntry.sent_at
        }
    });
});

// 4. View Dispatched Message Logs
app.get('/api/logs', (req, res) => {
    res.json({
        success: true,
        total: messageLogs.length,
        logs: messageLogs
    });
});

// Start Express HTTP Server
app.listen(PORT, () => {
    console.log(`=======================================================`);
    console.log(`🚀 RuangWarga WhatsApp Gateway (Node.js) is RUNNING!`);
    console.log(`📡 URL: http://localhost:${PORT}`);
    console.log(`🏥 Health Check: http://localhost:${PORT}/health`);
    console.log(`💬 Status: Connected & Ready to Dispatch Notifications`);
    console.log(`=======================================================`);
});
