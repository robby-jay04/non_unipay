<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful · Non-UniPay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            background: #0A63F3;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .screen {
            width: 100%;
            max-width: 360px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px 14px;
        }

        .header h1 {
            color: white;
            font-size: 20px;
            font-weight: 500;
        }

        .header .done {
            color: white;
            font-size: 15px;
            font-weight: 400;
            background: none;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .zigzag-top {
            width: 100%;
            height: 18px;
            background: white;
            clip-path: polygon(
                0% 100%, 3.33% 0%, 6.66% 100%, 9.99% 0%, 13.32% 100%,
                16.65% 0%, 19.98% 100%, 23.31% 0%, 26.64% 100%, 29.97% 0%,
                33.3% 100%, 36.63% 0%, 39.96% 100%, 43.29% 0%, 46.62% 100%,
                49.95% 0%, 53.28% 100%, 56.61% 0%, 59.94% 100%, 63.27% 0%,
                66.6% 100%, 69.93% 0%, 73.26% 100%, 76.59% 0%, 79.92% 100%,
                83.25% 0%, 86.58% 100%, 89.91% 0%, 93.24% 100%, 96.57% 0%,
                100% 100%, 100% 100%, 0% 100%
            );
        }

        .receipt-body {
            background: white;
            padding: 8px 24px 0;
        }

        .paid-to-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            color: #888;
            margin-bottom: 16px;
        }

        .paid-to-row svg {
            width: 15px;
            height: 15px;
            stroke: #aaa;
            fill: none;
            stroke-width: 1.8;
        }

        .merchant-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 600;
            color: #555;
            margin: 0 auto 10px;
        }

        .merchant-name {
            text-align: center;
            font-size: 15px;
            color: #333;
            margin-bottom: 6px;
        }

        .amount {
            text-align: center;
            font-size: 28px;
            font-weight: 600;
            color: #111;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .divider {
            height: 1px;
            background: #f0f0f0;
            margin: 0 -24px 14px;
        }

        .row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .row .label { color: #999; }
        .row .value { color: #333; }

        .ref-section {
            text-align: center;
            padding: 10px 0 8px;
        }

        .ref-label {
            font-size: 12px;
            color: #aaa;
            margin-bottom: 3px;
        }

        .ref-number {
            font-size: 13px;
            font-weight: 500;
            color: #333;
            letter-spacing: 0.3px;
        }

        .ref-date {
            font-size: 12px;
            color: #bbb;
            margin-top: 3px;
        }

        .note-section {
            text-align: center;
            padding: 14px 8px 16px;
        }

        .note-text {
            font-size: 12px;
            color: #aaa;
            line-height: 1.7;
            margin-bottom: 10px;
        }

        .gcash-scan {
            font-size: 14px;
            font-weight: 600;
            color: #111;
        }

        .gcash-scan span { color: #0A63F3; }

        .zigzag-bottom {
            width: 100%;
            height: 18px;
            background: white;
            clip-path: polygon(
                0% 0%, 3.33% 100%, 6.66% 0%, 9.99% 100%, 13.32% 0%,
                16.65% 100%, 19.98% 0%, 23.31% 100%, 26.64% 0%, 29.97% 100%,
                33.3% 0%, 36.63% 100%, 39.96% 0%, 43.29% 100%, 46.62% 0%,
                49.95% 100%, 53.28% 0%, 56.61% 100%, 59.94% 0%, 63.27% 100%,
                66.6% 0%, 69.93% 100%, 73.26% 0%, 76.59% 100%, 79.92% 0%,
                83.25% 100%, 86.58% 0%, 89.91% 100%, 93.24% 0%, 96.57% 100%,
                100% 0%, 100% 0%, 0% 0%
            );
        }

        .btn-area {
            padding: 20px 0 8px;
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 15px 20px;
            background: white;
            color: #0A63F3;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 14px;
            text-decoration: none;
            cursor: pointer;
            letter-spacing: 0.1px;
            transition: opacity 0.2s;
        }

        .btn-back:active { opacity: 0.85; }

        .btn-back .icon {
            width: 30px;
            height: 30px;
            background: #E6F1FB;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .btn-back .icon svg {
            width: 16px;
            height: 16px;
            stroke: #0A63F3;
            fill: none;
            stroke-width: 2;
        }

        .btn-back .arrow {
            margin-left: auto;
            font-size: 20px;
            line-height: 1;
        }

        .close-note {
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            margin-top: 12px;
            padding-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .close-note svg {
            width: 12px;
            height: 12px;
            stroke: rgba(255,255,255,0.5);
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <div class="screen">

        <div class="header">
            <h1>Payment</h1>
            <a href="#" class="done">Done</a>
        </div>

        <div class="zigzag-top"></div>

        <div class="receipt-body">

            <div class="paid-to-row">
                Successfully Paid To
                <svg viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-3-3m3 3l3-3"/></svg>
            </div>

            <div class="merchant-avatar">N</div>
            <p class="merchant-name">Non-UniPay</p>
            <p class="amount">{{ $amount }}</p>

            <div class="divider"></div>

            <div class="row">
    <span class="label">Amount Due</span>
    <span class="value">{{ $amount }}</span>
</div>
            <div class="row">
                <span class="label">Payment Method</span>
                <span class="value">GCash</span>
            </div>

            <div class="divider"></div>

            <div class="ref-section">
    <p class="ref-label">Ref. No.</p>
    <p class="ref-number">{{ $reference }}</p>
    <p class="ref-date">{{ $date }}</p>
</div>

            <div class="divider"></div>

            <div class="note-section">
                <p class="note-text">
                    {{ $message ?? 'Please show this screen for verification.' }}
               
            </div>

        </div>

        <div class="zigzag-bottom"></div>

        <div class="btn-area">
            <a href="nonunipay://payment-success" class="btn-back">
                <span class="icon">
                    <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><circle cx="12" cy="18" r="0.5" fill="#0A63F3"/></svg>
                </span>
                Back to Non-UniPay App
                <span class="arrow">›</span>
            </a>
        </div>

        <p class="close-note">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            If the app doesn't open, you may safely close this tab.
        </p>

    </div>
</body>
</html>