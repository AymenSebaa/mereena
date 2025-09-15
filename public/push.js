try {    
    urlBase64ToUint8Array = (base64String) => {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    window.addEventListener('load', () => {
        let btnSubscribe = document.querySelector('#btn_subscribe');
        let isEnabled = false;
        const blockedEl = `<span class="mr-1" >Unsupported Notifications</span> <i class="fa fa-ban" ></i>`;

        updateUI = (element, disabled) => {
            if (!btnSubscribe) return;
            btnSubscribe.innerHTML = element;
            btnSubscribe.disabled = disabled;
        }

        if (!("serviceWorker" in navigator)) {
            updateUI(blockedEl, true);
        }

        updateSubscription = async (register, method) => {
            let applicationServerKey = urlBase64ToUint8Array(pushPublicKey);
            const subscription = JSON.stringify(await register.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey }));
            const url = `${pushURL}?method=${method}`;
            const payload = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,         
                    'Accept': 'application/json'
                },
                body: subscription,
            }
            const data = await (await fetch(url, payload)).json();
            return data;
        }

        pushSubscribe = async () => {
            const register = await navigator.serviceWorker.register('app.js');
            setTimeout(async () => {
                if (isEnabled) {
                    let respons = await updateSubscription(register, 'delete');
                    setTimeout(() => {
                        register.unregister();
                        updateUI(respons.element);
                    }, 300);
                } else {
                    let respons = await updateSubscription(register, 'create');
                    updateUI(respons.element);
                }
                isEnabled = !isEnabled;
            }, 300);
        }

        navigator.serviceWorker.ready.then(async () => {
            const register = await navigator.serviceWorker.register('app.js');
            let respons = await updateSubscription(register, 'check');
            console.log('push status ', respons);
            isEnabled = respons.exist;
            updateUI(respons.element);
            if(!isEnabled) pushSubscribe();
        });
    });

} catch (err) {
    console.error(err);
}