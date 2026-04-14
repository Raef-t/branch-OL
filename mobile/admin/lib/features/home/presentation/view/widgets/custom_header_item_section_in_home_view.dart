import 'package:flutter/material.dart';
import '/core/components/filter_card_and_search_field_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/gen/assets.gen.dart';

class CustomHeaderItemSectionInHomeView extends StatelessWidget {
  const CustomHeaderItemSectionInHomeView({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left23AndRight21(
      context: context,
      child: FilterCardAndSearchFieldComponent(
        imageProvider: Assets.images.blueFilterImage.provider(),
        readOnly: true,
        onTap: () =>
            pushGoRouterHelper(context: context, view: kSearchViewRouter),
      ),
    );
  }
}
