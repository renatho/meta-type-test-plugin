import { compose } from "@wordpress/compose";
import { withSelect, withDispatch } from "@wordpress/data";
import { PluginSidebar } from "@wordpress/edit-post";
import { registerPlugin } from "@wordpress/plugins";

const META_TYPE = "my-meta-type";

const Component = ({ metadata, onChange }) => {
  return (
    <PluginSidebar title="Metadata test">
      <div>
        <button onClick={() => onChange([1, 2])}>1, 2</button>
        <button onClick={() => onChange([2, 3])}>2, 3</button>
      </div>
      <h3>Selected:</h3>
      <div>{metadata}</div>
    </PluginSidebar>
  );
};

const withMetadata = withSelect(select => ({
  metadata: select("core/editor").getEditedPostAttribute("meta")[META_TYPE]
}));

const withOnChange = withDispatch(dispatch => {
  const { editPost } = dispatch("core/editor");

  return {
    onChange(numbers) {
      editPost({
        meta: {
          [META_TYPE]: numbers
        }
      });
    }
  };
});

registerPlugin("plugin-name", {
  icon: "smiley",
  render: compose(withMetadata, withOnChange)(Component)
});
