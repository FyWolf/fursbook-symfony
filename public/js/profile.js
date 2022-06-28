
function openReportedUser(id) {
    const hidder = document.getElementById("modalBackground");
    const modal = document.getElementById("modalDiv");
    const modalContent = document.getElementById("modalContent");
    let content = `
        <form onSubmit="return sendUserReport(${id})">
        <label for="reason">Report category</label>
        <select name="reason" id="modalSelect">
        </select>
        <label for="description">Description</label>
        <textarea name="description" id="modalDesc" maxLength="255"></textarea>
        <button type="submit">Send</button>
        </form>
    `;
    modalContent.innerHTML = content;
    const select = document.getElementById("modalSelect");
    $.post(
        window.location.pathname,
        {
        'action': "getReportReason",
        },
        function (response) {
        response.reasonList.forEach(element => {
            let content = `
            <option value="${element.id}">${element.name}</option>
            `
            select.innerHTML += content;
        });
        },
    );
    hidder.classList.remove("hidden");
    modal.classList.remove("hidden");
}

function sendUserReport(id) {
    const select = document.getElementById("modalSelect");
    const desc = document.getElementById("modalDesc");
    $.post(
        window.location.pathname,
        {
            'action': "sendUserReport",
            'targetId': id,
            'reasonId': select.value,
            'description': desc.value,
        },
        function (response) {
            sendAlert("The report has been successfully sent", "success");
            closeModal();
        },
    );
    return false;
}