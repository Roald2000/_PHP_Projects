const useApi = (baseURL) => {
    return {
        post: async (url, payload) => {
            return await fetch(`${baseURL}${url}`, { method: 'POST', body: JSON.stringify(payload), 'Content-Type': 'application/json' }).then(response => response.json());
        },
        put: async (url, payload) => {
            return await fetch(`${baseURL}${url}`, { method: 'PUT', body: JSON.stringify(payload), 'Content-Type': 'application/json' }).then(response => response.json());
        },
        get: async (url) => {
            return await fetch(`${baseURL}${url}`, { method: 'GET' }).then(response => response.json());
        },
        delete: async (url) => {
            return await fetch(`${baseURL}${url}`, { method: 'DELETE' });
        }
    }
}
/*
    * How it is
        * http:///domain:port/directory/path/file.php?
    * How it should look
        * http://localhost:80/_php_projects/php_mysql_crud/php-mysql-javascript_crud/php_rest_api.php?
*/

let myapi = useApi('http://localhost:80/_PHP_Projects/php-mysql-javascript_crud/php_rest_api.php?');









