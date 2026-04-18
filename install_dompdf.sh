#!/bin/bash
# ============================================================
# install_dompdf.sh — Install dompdf via Composer
# Run from the guardvax/ root directory
# ============================================================

echo "=============================================="
echo "  GuardVAX — Installing dompdf via Composer"
echo "=============================================="

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "Composer not found. Installing..."
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
fi

# Install dompdf
composer require dompdf/dompdf

echo ""
echo "✅ dompdf installed successfully!"
echo ""
echo "Vendor autoload path: vendor/autoload.php"
echo ""
echo "If you cannot use Composer, download manually:"
echo "  https://github.com/dompdf/dompdf/releases"
echo "  Then place in: guardvax/vendor/dompdf/"
