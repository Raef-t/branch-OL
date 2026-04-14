import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import '/core/components/svg_image_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/styles/colors_style.dart';
import '/features/search/presentation/view/widgets/custom_two_images_with_text_in_search_view.dart';
import '/gen/assets.gen.dart';

class CustomGeneratePreviouslySearchInSearchView extends StatelessWidget {
  const CustomGeneratePreviouslySearchInSearchView({super.key});

  @override
  Widget build(BuildContext context) {
    return OnlyPaddingWithChild.left35AndRight25(
      context: context,
      child: Column(
        children: [
          CustomTwoImagesWithTextInSearchView(
            text: 'صفحة الرئيسية',
            onPressed: () =>
                pushGoRouterHelper(context: context, view: kHomeViewRouter),
            image: SvgImageComponent(
              pathImage: Assets.images.homeImage,
              color: ColorsStyle.mediumBlackColor2,
            ),
          ),
          CustomTwoImagesWithTextInSearchView(
            text: 'صفحة الدورات',
            onPressed: () =>
                pushGoRouterHelper(context: context, view: kCoursesViewRouter),
            image: SvgPicture.asset(Assets.images.coursesImage),
          ),
          CustomTwoImagesWithTextInSearchView(
            text: 'صفحة المدرسون',
            onPressed: () =>
                pushGoRouterHelper(context: context, view: kTeachersViewRouter),
            image: SvgPicture.asset(Assets.images.teachersImage),
          ),
          CustomTwoImagesWithTextInSearchView(
            text: 'صفحة البروفايل',
            onPressed: () =>
                pushGoRouterHelper(context: context, view: kProfileViewRouter),
            image: SvgPicture.asset(Assets.images.profilesImage),
          ),
          CustomTwoImagesWithTextInSearchView(
            text: 'صفحة المذاكرات اليومية للمعهد',
            onPressed: () => pushGoRouterHelper(
              context: context,
              view: kExamViewToHoleAcademicRouter,
            ),
            image: SvgPicture.asset(Assets.images.coursesImage),
          ),
          CustomTwoImagesWithTextInSearchView(
            text: 'صفحة برنامج الدوام اليومي للمعهد',
            onPressed: () => pushGoRouterHelper(
              context: context,
              view: kWorkHoursViewRouter,
            ),
            image: SvgPicture.asset(Assets.images.coursesImage),
          ),
        ],
      ),
    );
  }
}
