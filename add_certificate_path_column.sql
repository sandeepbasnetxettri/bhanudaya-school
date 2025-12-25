-- Script to add certificate_path column to results table
-- This column was missing from the original schema but referenced in the code

-- Add the certificate_path column to the results table
ALTER TABLE results 
ADD COLUMN certificate_path VARCHAR(500) NULL AFTER percentage;

-- Add an index for better performance when querying by certificate_path
CREATE INDEX idx_results_certificate_path ON results(certificate_path);