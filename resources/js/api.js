const prepareApi = () => {
    let token = document
        .querySelector(`meta[name='csrf-token']`)
        .getAttribute(`content`);

    return {
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json, text-plain, */*",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": token
        },
        method: "post",
        credentials: "same-origin"
    };
};

async function fetchData(url, body = {}) {
    let args = prepareApi();
    args.body = JSON.stringify(body);
    let response = await fetch(url, args);
    return await response.json();
}

async function getData(url) {
    let args = prepareApi();
    args.method = "get";
    let response = await fetch(url, args);
    return await response.json();
}

export { fetchData, getData };
