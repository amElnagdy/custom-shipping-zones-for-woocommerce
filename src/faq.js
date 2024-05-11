import React from "react";
import { Collapse } from "antd";

const { Panel } = Collapse;

const FAQ = ({ faqs }) => {
  return (
    <Collapse accordion>
      {faqs.map((faq, index) => (
        <Panel header={faq.question} key={index}>
          <p>{faq.answer}</p>
        </Panel>
      ))}
    </Collapse>
  );
};

export default FAQ;