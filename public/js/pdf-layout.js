(function () {
    'use strict';
    window.applyPdfPageNumbers = function (pdf) {
        var count = pdf.getNumberOfPages();
        for (var page = 1; page <= count; page++) {
            pdf.setPage(page);
            pdf.setFont('helvetica', 'normal');
            pdf.setFontSize(8);
            pdf.setTextColor(110, 123, 140);
            pdf.text(page + '/' + count, pdf.internal.pageSize.getWidth() / 2,
                pdf.internal.pageSize.getHeight() - 12, { align: 'center' });
        }
        return pdf;
    };
}());
