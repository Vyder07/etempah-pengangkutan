<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maklumat Tempahan - {{ $booking->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }

        .header h1 {
            color: #1e40af;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            color: #6b7280;
            font-size: 11px;
        }

        .booking-id {
            background: #dbeafe;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #1e40af;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-completed { background: #dbeafe; color: #1e40af; }
        .status-cancelled { background: #f3f4f6; color: #374151; }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .info-table td {
            padding: 10px 5px;
        }

        .info-table td:first-child {
            width: 35%;
            font-weight: bold;
            color: #4b5563;
        }

        .info-table td:last-child {
            width: 65%;
            color: #1f2937;
        }

        .vehicle-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .date-range {
            background: #fef3c7;
            padding: 12px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .date-range strong {
            color: #92400e;
        }

        .notes-box {
            background: #fef9e7;
            padding: 12px;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SISTEM PENGURUSAN TEMPAHAN KENDERAAN</h1>
        <p>Maklumat Tempahan Lengkap</p>
    </div>

    <!-- Booking ID -->
    <div class="booking-id">
        ID TEMPAHAN: #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
        <span class="status-badge status-{{ $booking->status }}">{{ strtoupper($booking->status) }}</span>
    </div>

    <!-- User Information -->
    <div class="section">
        <div class="section-title">📋 MAKLUMAT PEMOHON</div>
        <table class="info-table">
            <tr>
                <td>Nama Pemohon</td>
                <td>{{ $booking->user->name }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $booking->user->email }}</td>
            </tr>
            <tr>
                <td>Tarikh Permohonan</td>
                <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <!-- Vehicle Information -->
    <div class="section">
        <div class="section-title">🚗 MAKLUMAT KENDERAAN</div>
        <div class="vehicle-box">
            <table class="info-table">
                <tr>
                    <td>Jenis Kenderaan</td>
                    <td><strong>{{ strtoupper($booking->vehicle_type ?? 'N/A') }}</strong></td>
                </tr>
                <tr>
                    <td>Nama Kenderaan</td>
                    <td>{{ $booking->vehicle_name }}</td>
                </tr>
                <tr>
                    <td>No. Pendaftaran</td>
                    <td><strong>{{ $booking->vehicle_plate }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Booking Details -->
    <div class="section">
        <div class="section-title">📅 BUTIRAN TEMPAHAN</div>

        <div class="date-range">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <strong>Tarikh Mula:</strong><br>
                        {{ $booking->start_date->format('d/m/Y H:i') }}
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <strong>Tarikh Tamat:</strong><br>
                        {{ $booking->end_date->format('d/m/Y H:i') }}
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td>Destinasi</td>
                <td>{{ $booking->destination }}</td>
            </tr>
            <tr>
                <td>Tujuan Tempahan</td>
                <td>{{ $booking->purpose }}</td>
            </tr>
            <tr>
                <td>Status Tempahan</td>
                <td><span class="status-badge status-{{ $booking->status }}">{{ strtoupper($booking->status) }}</span></td>
            </tr>
            @if($booking->notes)
            <tr>
                <td colspan="2">
                    <div class="notes-box">
                        <strong>📝 Nota Admin:</strong><br>
                        {{ $booking->notes }}
                    </div>
                </td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Dokumen ini dijana secara automatik oleh sistem</strong></p>
        <p>Tarikh Cetak: {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>© {{ date('Y') }} Sistem Pengurusan Tempahan Kenderaan. Hak Cipta Terpelihara.</p>
    </div>
</body>
</html>
