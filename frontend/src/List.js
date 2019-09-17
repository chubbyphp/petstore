import React, { useState, useEffect } from 'react';
import { Spinner } from 'react-bootstrap';

const List = ({ entity }) => {
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState(null);
    const [data, setData] = useState(null);

    useEffect(() => {
        if (!entity) {
            return;
        }

        setIsLoading(true);
        setError(false);
        setData(null);

        let status;

        fetch('https://petstore.development/api/pets', {
            'Accept': 'application/json'
        })
            .then((res) => {
                status = res.status;
                return res.json();
            })
            .then((json) => {
                console.log(status);

                setIsLoading(false);
                setData(json);
            })
            .catch((error) => setError(error || 'cors?!!'))
        ;
    }, [entity]);

    if (!entity) {
        return (<span>Please select entity</span>);
    }

    if (isLoading) {
        return (<Spinner animation="border" />);
    }

    if (error) {
        return (<span class="error">Please select entity</span>);
    }

    if (!data) {
        return null;
    }

    return (<table>
        <thead>
            <tr>
                <td>id</td>
            </tr>
        </thead>
        <tbody>
            {data._embedded.items.map((item) => (
                <tr key={item.id}>
                    <td>{item.id}</td>
                </tr>
            ))}
        </tbody>
    </table>);
};

export default List;
