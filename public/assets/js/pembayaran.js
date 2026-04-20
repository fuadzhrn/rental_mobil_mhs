document.addEventListener("DOMContentLoaded", () => {
    const methodItems = Array.from(document.querySelectorAll(".method-item"));
    const accountNumber = document.querySelector("#accountNumber");
    const receiverName = document.querySelector("#receiverName");
    const paymentCode = document.querySelector("#paymentCode");
    const proofFile = document.querySelector("#proofFile");
    const selectedFileName = document.querySelector("#selectedFileName");

    const paymentMap = {
        bank: {
            account: "1234567890",
            receiver: "PT Velora Mobilitas Nusantara",
            code: "VLR-240417-9012",
        },
        va: {
            account: "8808123412345678",
            receiver: "VA Velora Mobility",
            code: "VA-VLR-240417-6543",
        },
        ewallet: {
            account: "0812-7788-9900",
            receiver: "VeloraRide Official",
            code: "EW-VLR-240417-1290",
        },
        gateway: {
            account: "PG-SESSION-473820",
            receiver: "Velora Secure Gateway",
            code: "PG-VLR-240417-7412",
        },
    };

    methodItems.forEach((item) => {
        item.addEventListener("click", () => {
            methodItems.forEach((el) => el.classList.remove("is-active"));
            item.classList.add("is-active");

            const key = item.dataset.method;
            const selected = paymentMap[key];
            if (!selected) {
                return;
            }

            if (accountNumber) {
                accountNumber.textContent = selected.account;
            }
            if (receiverName) {
                receiverName.textContent = selected.receiver;
            }
            if (paymentCode) {
                paymentCode.textContent = selected.code;
            }
        });
    });

    if (proofFile && selectedFileName) {
        proofFile.addEventListener("change", () => {
            if (proofFile.files && proofFile.files.length > 0) {
                selectedFileName.textContent = proofFile.files[0].name;
            } else {
                selectedFileName.textContent = "belum ada file";
            }
        });
    }
});
