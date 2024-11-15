CREATE TABLE Loan (
    LoanId INT AUTO_INCREMENT PRIMARY KEY,
    LoanType VARCHAR(50) NOT NULL,
    Amount DECIMAL(10, 2) NOT NULL,
    InterestRate DECIMAL(5, 2) NOT NULL,
    CustomerId INT NOT NULL,
    Status VARCHAR(20) DEFAULT 'Pending',
    StartDate DATE NOT NULL,
    EndDate DATE NOT NULL,
    FOREIGN KEY (CustomerId) REFERENCES Customer(CustomerId)
);
jubjhrtppgotstfp