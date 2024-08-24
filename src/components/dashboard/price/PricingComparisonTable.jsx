import React from "react";
import { __ } from "@wordpress/i18n";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import PricingFeatureRow from "./PricingFeatureRow";

const planFeatures = [
    { 
      type: "Financial data",
      features: [
        {
          name: "Open/High/Low/Close",
          free: true,
          startup: true,
          team: true,
          enterprise: true,
        },
        {
          name: "Price-volume difference indicator",
          free: true,
          startup: true,
          team: true,
          enterprise: true,
        },
      ],
    },
    {
      type: "On-chain data",
      features: [
        {
          name: "Network growth",
          free: true,
          startup: false,
          team: true,
          enterprise: true,
        },
        {
          name: "Average token age consumed",
          free: true,
          startup: false,
          team: true,
          enterprise: true,
        },
        {
          name: "Exchange flow",
          free: false,
          startup: false,
          team: true,
          enterprise: true,
        },
        {
          name: "Total ERC20 exchange funds flow",
          free: false,
          startup: false,
          team: true,
          enterprise: true,
        },
      ],
    },
    {
      type: "Social data",
      features: [
        {
          name: "Dev activity",
          free: false,
          startup: true,
          team: false,
          enterprise: true,
        },
        {
          name: "Topic search",
          free: true,
          startup: true,
          team: true,
          enterprise: true,
        },
        {
          name: "Relative social dominance",
          free: true,
          startup: true,
          team: false,
          enterprise: true,
        },
      ],
    },
  ];

export default function PricingComparisonTable() {
  return (
    <div className="mt-20 lg:mt-32">
      <div className="mb-10 lg:text-center lg:mb-20">
        <h3 className="text-2xl font-semibold dark:text-white">
          {__("Compare plans", "lnd-master-dev")}
        </h3>
      </div>
      <Table className="hidden lg:table">
        <TableHeader>
          <TableRow className="bg-muted hover:bg-muted">
            <TableHead className="w-3/12 text-primary">{__("Plans", "lnd-master-dev")}</TableHead>
            <TableHead className="w-2/12 text-lg font-medium text-center text-primary">
              {__("Free", "lnd-master-dev")}
            </TableHead>
            <TableHead className="w-2/12 text-lg font-medium text-center text-primary">
              {__("Startup", "lnd-master-dev")}
            </TableHead>
            <TableHead className="w-2/12 text-lg font-medium text-center text-primary">
              {__("Team", "lnd-master-dev")}
            </TableHead>
            <TableHead className="w-2/12 text-lg font-medium text-center text-primary">
              {__("Enterprise", "lnd-master-dev")}
            </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {planFeatures.map((featureType) => (
            <React.Fragment key={featureType.type}>
              <TableRow className="bg-muted/50">
                <TableCell colSpan={5} className="font-bold">
                  {__(featureType.type, "lnd-master-dev")}
                </TableCell>
              </TableRow>
              {featureType.features.map((feature) => (
                <PricingFeatureRow key={feature.name} feature={feature} />
              ))}
            </React.Fragment>
          ))}
        </TableBody>
      </Table>
      {/* Mobile version of the comparison table */}
      {/* ... (Keep the existing mobile version code) */}
    </div>
  );
}