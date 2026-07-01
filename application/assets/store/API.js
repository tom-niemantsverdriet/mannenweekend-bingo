import axios from 'axios';

export default () => {
    let headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }

    let api = axios.create({
        baseURL: '/app-api/',
        headers: headers
    });

    return api;
}
