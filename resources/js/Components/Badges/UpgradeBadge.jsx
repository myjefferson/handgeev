import React from 'react';

const UpgradeBadge = ({title = 'PRO'}) => {
    return (
        <span className="inline-flex items-center ml-2 px-2 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-sm">
            {title}
        </span>
    );
};

export default UpgradeBadge;