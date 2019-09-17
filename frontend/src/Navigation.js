import React from 'react';
import { Button } from 'react-bootstrap';

const Navigation = ({onClick}) => {
    return (<ul><li><Button onClick={() => onClick('pets')} variant="link">Pets</Button></li></ul>);
};

export default Navigation;
