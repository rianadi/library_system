import './bootstrap';

import Alpine from 'alpinejs';
import { Html5Qrcode } from 'html5-qrcode';

window.Alpine = Alpine;

Alpine.start();

window.openLoanModal = () => {};
window.closeLoanModal = () => {};

function initOfflineLoanModal() {
    const modal = document.querySelector('[data-loan-modal]');

    if (!modal) {
        return;
    }

    const lookupUrl = modal.dataset.lookupUrl;
    const bookCodeInput = modal.querySelector('[data-book-code-input]');
    const bookIdInput = modal.querySelector('[data-book-id]');
    const bookTitleInput = modal.querySelector('[data-book-title]');
    const bookAuthorInput = modal.querySelector('[data-book-author]');
    const bookStockInput = modal.querySelector('[data-book-stock]');
    const bookLocationInput = modal.querySelector('[data-book-location]');
    const lookupMessage = modal.querySelector('[data-book-lookup-message]');
    const submitButton = modal.querySelector('[data-offline-loan-submit]');
    const scannerPanel = modal.querySelector('[data-scanner-panel]');
    const scannerMessage = modal.querySelector('[data-scanner-message]');
    const scanButton = modal.querySelector('[data-scan-button]');
    const stopScanButton = modal.querySelector('[data-stop-scan-button]');

    let stream = null;
    let scanner = null;
    let scanning = false;

    const setMessage = (message, type = 'muted') => {
        if (!lookupMessage) {
            return;
        }

        lookupMessage.textContent = message;
        lookupMessage.classList.remove('text-slate-500', 'text-red-600', 'text-emerald-700', 'text-amber-700');
        lookupMessage.classList.add({
            error: 'text-red-600',
            success: 'text-emerald-700',
            warning: 'text-amber-700',
            muted: 'text-slate-500',
        }[type] || 'text-slate-500');
    };

    const setSubmitState = () => {
        if (submitButton) {
            submitButton.disabled = !bookIdInput?.value;
        }
    };

    const clearBook = () => {
        if (bookIdInput) bookIdInput.value = '';
        if (bookTitleInput) bookTitleInput.value = '';
        if (bookAuthorInput) bookAuthorInput.value = '';
        if (bookStockInput) bookStockInput.value = '';
        if (bookLocationInput) bookLocationInput.value = '';
        setSubmitState();
    };

    const fillBook = (book) => {
        const availableCopies = Number(book.available_copies || 0);

        if (bookIdInput) {
            bookIdInput.value = availableCopies > 0 ? book.id : '';
        }

        if (bookTitleInput) bookTitleInput.value = book.title || '';
        if (bookAuthorInput) bookAuthorInput.value = book.author || '';
        if (bookStockInput) {
            bookStockInput.value = `${availableCopies} dari ${book.total_copies || 0} tersedia`;
        }
        if (bookLocationInput) bookLocationInput.value = book.location || '';

        if (availableCopies > 0) {
            setMessage(`Buku ditemukan: ${book.title}`, 'success');
        } else {
            setMessage('Buku ditemukan, tetapi stok sedang habis.', 'warning');
        }

        setSubmitState();
    };

    const lookupBook = async (rawCode) => {
        const code = String(rawCode || '').trim();

        if (!code) {
            clearBook();
            setMessage('');
            return;
        }

        setMessage('Mencari data buku...', 'muted');

        try {
            const response = await fetch(`${lookupUrl.replace(/\/$/, '')}/${encodeURIComponent(code)}`, {
                headers: {
                    Accept: 'application/json',
                },
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                clearBook();
                setMessage(data.message || 'Buku tidak ditemukan.', 'error');
                return;
            }

            fillBook(data.book);
        } catch (error) {
            clearBook();
            setMessage('Gagal membaca data barcode. Coba input kode secara manual.', 'error');
        }
    };

    const stopScanner = () => {
        scanning = false;

        if (scanner) {
            scanner.stop().then(() => {
                scanner = null;
            }).catch(() => {
                scanner = null;
            });
        }

        scannerPanel?.classList.add('hidden');
    };

    const startScanner = async () => {
        try {
            if (scanner) {
                return;
            }

            scanning = true;
            scannerPanel?.classList.remove('hidden');

            // Create scanner instance
            scanner = new Html5Qrcode('scanner-video-container');

            if (scannerMessage) {
                scannerMessage.textContent = 'Kamera aktif. Arahkan barcode/QR code ke kamera.';
            }

            const qrCodeSuccessCallback = (code) => {
                bookCodeInput.value = code;
                lookupBook(code);
                stopScanner();
            };

            const qrCodeErrorCallback = () => {
                // Silent error - just keep scanning
            };

            await scanner.start(
                { facingMode: 'environment' },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                },
                qrCodeSuccessCallback,
                qrCodeErrorCallback
            );
        } catch (error) {
            stopScanner();
            if (scannerMessage) {
                scannerMessage.textContent = 'Kamera tidak bisa dibuka. Periksa izin akses kamera.';
            }
            setMessage('Kamera tidak bisa dibuka. Pastikan izin kamera aktif atau input kode manual.', 'error');
        }
    };

    window.openLoanModal = () => {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            const firstEmptyField = modal.querySelector('[name="user_id"]:not([value]), [data-book-code-input]');
            firstEmptyField?.focus();
        }, 50);
    };

    window.closeLoanModal = () => {
        stopScanner();
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    bookCodeInput?.addEventListener('change', () => lookupBook(bookCodeInput.value));
    bookCodeInput?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            lookupBook(bookCodeInput.value);
        }
    });

    scanButton?.addEventListener('click', startScanner);
    stopScanButton?.addEventListener('click', stopScanner);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            window.closeLoanModal();
        }
    });

    setSubmitState();

    if (modal.dataset.openOnLoad === 'true') {
        window.openLoanModal();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOfflineLoanModal);
} else {
    initOfflineLoanModal();
}
