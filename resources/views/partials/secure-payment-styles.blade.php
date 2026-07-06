<style>
    /* Shared Secure Payment modal — web register + web renew */
    .sp-secure-modal {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        background: #fff;
    }

    .sp-secure-header {
        background: linear-gradient(135deg, #643271 0%, #4e2a84 100%);
        min-height: 110px;
        padding: 28px 48px 24px !important;
        border-bottom: none !important;
        text-align: center;
        position: relative;
    }

    .sp-secure-title {
        font-family: 'EB Garamond', Georgia, serif;
        font-weight: 700;
        font-size: 2.25rem;
        letter-spacing: 0.5px;
        color: #d98b1c;
        text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        margin: 0;
        line-height: 1.2;
    }

    .sp-secure-subtitle {
        color: rgba(255, 255, 255, 0.85);
        font-size: 0.875rem;
        margin-top: 6px;
    }

    .sp-secure-close {
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 10;
        opacity: 1;
    }

    .sp-billing-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sp-billing-option {
        display: block;
        margin: 0;
        cursor: pointer;
    }

    .sp-billing-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .sp-billing-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 2px solid #e8e8ed;
        border-radius: 12px;
        background: #fff;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }

    .sp-billing-option input:checked + .sp-billing-card {
        border-color: #643271;
        background: #faf6fc;
        box-shadow: 0 0 0 1px rgba(100, 50, 113, 0.15);
    }

    .sp-radio-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #c5c5d0;
        flex-shrink: 0;
        position: relative;
        background: #fff;
    }

    .sp-billing-option input:checked + .sp-billing-card .sp-radio-dot {
        border-color: #643271;
    }

    .sp-billing-option input:checked + .sp-billing-card .sp-radio-dot::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 10px;
        height: 10px;
        margin: -5px 0 0 -5px;
        border-radius: 50%;
        background: #643271;
    }

    .sp-billing-text {
        flex: 1;
        min-width: 0;
    }

    .sp-billing-label {
        display: block;
        font-weight: 700;
        font-size: 0.95rem;
        color: #222;
    }

    .sp-billing-price {
        display: block;
        font-size: 0.85rem;
        color: #643271;
        font-weight: 600;
    }

    .sp-apply-btn {
        background: #643271 !important;
        color: #ffffff !important;
        border: none !important;
        font-weight: 600;
    }

    .sp-apply-btn:hover,
    .sp-apply-btn:focus {
        background: #532660 !important;
        color: #ffffff !important;
    }

    .sp-apply-btn:disabled {
        opacity: 0.65;
        color: #ffffff !important;
    }

    .sp-pay-btn {
        background: #643271 !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
    }

    .sp-pay-btn:hover {
        background: #532660 !important;
        color: #ffffff !important;
    }
</style>
