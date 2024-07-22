import React, { useState, useEffect } from "react";

const HelloWorld = () => {
  const text = "Hello World ! ";
  const [displayedText, setDisplayedText] = useState("");
  const [index, setIndex] = useState(0);

  useEffect(() => {
    const interval = setInterval(() => {
      setDisplayedText((prev) => prev + text[index]);
      setIndex((prev) => (prev + 1) % text.length);
    }, 200);

    return () => clearInterval(interval);
  }, [index, text]);

  useEffect(() => {
    if (index === 0) {
      setDisplayedText("");
    }
  }, [index]);

  return <div>{displayedText}</div>;
};

export default HelloWorld;
