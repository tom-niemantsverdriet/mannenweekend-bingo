module.exports = function applyFlowtogetherVueConfig(webpackConfig, context)
{
    let path = context.path;
    let projectRoot = context.projectRoot;
    let lumiFlowtogetherRoot = path.resolve(projectRoot, '../Lumi/flowtogether-vue');

    // Add flowtogether-vue include paths for Vue and JS handling

    let vueRule = webpackConfig.module.rules.find((rule) => String(rule.test) === String(/\.vue$/));
    let javascriptRule = webpackConfig.module.rules.find((rule) => String(rule.test) === String(/\.js$/));

    if (vueRule !== undefined) {
        addIncludePath(vueRule.include, path.resolve(lumiFlowtogetherRoot, 'application/assets/vue'));
    }

    if (javascriptRule !== undefined) {
        addIncludePath(javascriptRule.include, path.resolve(lumiFlowtogetherRoot, 'application/assets/js'));
        addIncludePath(javascriptRule.include, path.resolve(lumiFlowtogetherRoot, 'application/assets/vue'));
    }

    // Register flowtogether-vue aliases

    webpackConfig.resolve.alias['@flowtogether'] = path.resolve(
        lumiFlowtogetherRoot,
        'application/assets/js/app/components.js'
    );

    webpackConfig.resolve.alias['@flowtogether-vue'] = path.resolve(
        lumiFlowtogetherRoot,
        'application/assets/js/app'
    );

    webpackConfig.resolve.alias['@flowtogether-components'] = path.resolve(
        lumiFlowtogetherRoot,
        'application/assets/vue/app/components'
    );
};

function addIncludePath(includePaths, includePath)
{
    if (!includePaths.includes(includePath)) {
        includePaths.push(includePath);
    }
}
