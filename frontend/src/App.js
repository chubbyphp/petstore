import React, { useState } from 'react';
import { Container, Row, Col } from 'react-bootstrap';
import Navigation from './Navigation';
import List from './List';

function App() {
  const [entity, setEntity] = useState(null);

  return (
      <Container>
        <Row>
          <Col sm={12}>Header</Col>
        </Row>
        <Row>
          <Col sm={4}><Navigation onClick={setEntity}></Navigation></Col>
          <Col sm={8}><List entity={entity}></List></Col>
        </Row>
      </Container>
  );
}

export default App;
